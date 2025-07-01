<?php
class PageController
{
    private $userModel;
    private $pageModel;

    public function __construct(UserModel $userModel, PageModel $pageModel)
    {
        $this->userModel = $userModel;
        $this->pageModel = $pageModel;
    }

    /**
     * Exibe a página inicial
     */
    public function home()
    {
        $user  = $this->getCurrentUser();
        $areas = $this->userModel->getAreasAtuacao();

        $this->render('home', [
            'title'         => 'Bem-vindo ao Curriculum Quiz',
            'areas'         => $areas,
            'user'          => $user,
            'nomeUsuario'   => $user ? $user['nome'] : 'Visitante',
            'usuarioLogado' => $this->isLoggedIn(),
            'dados'         => [
                'titulo'    => 'Bem-vindo ao Curriculum Quiz',
                'descricao' => 'Sistema de avaliação de conhecimentos para profissionais de ADS',
                'usuario'   => $user,
            ],
        ]);
    }

    public function mostrarCadastro()
{
    $data = [
        'errors' => $_SESSION['register_errors'] ?? [],
        'dados' => [],
        'areas' => $this->userModel->getAreasAtuacao()
    ];

    // Se usuário está logado, carrega seus dados
    if (isset($_SESSION['user_id'])) {
        $data['dados'] = $this->userModel->getUserById($_SESSION['user_id']);
    }

    $this->render('cadastro', $data);
    unset($_SESSION['register_errors']);
}

    /**
     * Busca currículos com filtros
     */
    public function buscarCurriculos()
    {
        $areaFilter = $_GET['area'] ?? null;
        $page       = max((int) ($_GET['page'] ?? 1), 1);
        $perPage    = 10;
        $offset     = ($page - 1) * $perPage;

        $results = $this->pageModel->getCurriculos($areaFilter, $perPage, $offset);
        $total   = $this->pageModel->countCurriculos($areaFilter);
        $areas   = $this->userModel->getAreasAtuacao();

        $this->render('busca', [
            'title'         => 'Busca de Currículos',
            'resultados'    => $results,
            'areas'         => $areas,
            'paginaAtual'   => $page,
            'totalPaginas'  => ceil($total / $perPage),
            'areaFiltro'    => $areaFilter,
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser() ? $this->getCurrentUser()['nome'] : 'Visitante',
            'usuarioLogado' => $this->isLoggedIn(),
        ]);
    }

    /**
     * Exibe a tela de seleção de quiz
     */
    public function mostrarQuiz()
    {
        $this->render('selecionar', [
            'title'         => 'Selecionar Quiz',
            'areas'         => $this->userModel->getAreasAtuacao(),
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser() ? $this->getCurrentUser()['nome'] : 'Visitante',
            'usuarioLogado' => $this->isLoggedIn(),
            'dados'         => [
                'titulo'    => 'Selecione um Quiz',
                'descricao' => 'Escolha uma área de atuação para começar o quiz',
            ],
        ]);
    }

    /**
     * Inicia um novo quiz
     */
    public function iniciarQuiz()
    {
        if (! $this->isLoggedIn()) {
            $_SESSION['redirect_url'] = '?action=startQuiz';
            $this->redirect('/login');
            return;
        }

        $areaId = $_GET['area'] ?? null;
        if (! $areaId) {
            $this->renderError('Área não especificada');
            return;
        }

        $questions = $this->pageModel->getPerguntasAleatorias($areaId);

        if (count($questions) < 5) {
            $this->renderError('Não há perguntas suficientes para esta área');
            return;
        }

        $this->render('quiz', [
            'title'          => 'Quiz em Andamento',
            'perguntas'      => $questions,
            'totalPerguntas' => count($questions),
            'areaId'         => $areaId,
            'user'           => $this->getCurrentUser(),
            'nomeUsuario'    => $this->getCurrentUser()['nome'],
            'usuarioLogado'  => true,
        ]);
    }

    /**
     * Processa as respostas do quiz
     */
    public function finalizarQuiz()
    {
        header('Content-Type: application/json');

        if (! $this->isLoggedIn()) {
            echo json_encode(['error' => 'Não autenticado']);
            http_response_code(401);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (! $data || ! isset($data['respostas'])) {
            echo json_encode(['error' => 'Dados inválidos']);
            http_response_code(400);
            exit();
        }

        $correct     = 0;
        $questionIds = $data['perguntaIds'] ?? [];
        $answers     = $data['respostas'];

        for ($i = 0; $i < count($questionIds); $i++) {
            if ($this->pageModel->verificarResposta($questionIds[$i], $answers[$i])) {
                $correct++;
            }
        }

        $percentage = round(($correct / count($questionIds)) * 100);
        $userId     = $_SESSION['user_id'];

        $this->pageModel->updateUserScore($userId, $correct, count($questionIds));

        echo json_encode([
            'success'    => true,
            'correct'    => $correct,
            'percentage' => $percentage,
            'total'      => count($questionIds),
        ]);
        exit();
    }

    /**
     * Gerencia o painel administrativo
     */
    public function gerenciarQuiz()
    {
        if (! $this->isAdmin()) {
            $this->redirect('/login');
            return;
        }

        $page    = max((int) ($_GET['page'] ?? 1), 1);
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $questions      = $this->pageModel->getPerguntasPaginadas($perPage, $offset);
        $totalQuestions = $this->pageModel->countPerguntas();

        $this->render('admin', [
            'title'          => 'Painel Admin',
            'perguntas'      => $questions,
            'paginaAtual'    => $page,
            'totalPerguntas' => $totalQuestions,
            'totalPaginas'   => ceil($totalQuestions / $perPage),
            'user'           => $this->getCurrentUser(),
            'nomeUsuario'    => $this->getCurrentUser()['nome'],
            'usuarioLogado'  => true,
        ]);
    }

    /**
     * Adiciona uma nova pergunta
     */
    public function adicionarPergunta()
    {
        if (! $this->isAdmin()) {
            echo json_encode(['error' => 'Acesso negado']);
            http_response_code(403);
            exit();
        }

        $dados = [
            'pergunta'         => $_POST['pergunta'],
            'resposta_correta' => $_POST['resposta_correta'],
            'alternativa1'     => $_POST['alternativa1'],
            'alternativa2'     => $_POST['alternativa2'],
            'alternativa3'     => $_POST['alternativa3'],
            'area_atuacao_id'  => $_POST['area_atuacao_id'],
            'nivel_id'         => $_POST['nivel_id'],
        ];

        $success = $this->pageModel->addPergunta($dados);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    /**
     * Exclui uma pergunta
     */
    public function excluirPergunta()
    {
        if (! $this->isAdmin()) {
            echo json_encode(['error' => 'Acesso negado']);
            http_response_code(403);
            exit();
        }

        $perguntaId = $_POST['id'] ?? null;
        if (! $perguntaId) {
            echo json_encode(['error' => 'ID da pergunta não fornecido']);
            http_response_code(400);
            exit();
        }

        $success = $this->pageModel->deletePergunta($perguntaId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    /**
     * Alterna o status de uma pergunta
     */
    public function toggleStatus()
    {
        if (! $this->isAdmin()) {
            echo json_encode(['error' => 'Acesso negado']);
            http_response_code(403);
            exit();
        }

        $perguntaId = $_POST['id'] ?? null;
        if (! $perguntaId) {
            echo json_encode(['error' => 'ID da pergunta não fornecido']);
            http_response_code(400);
            exit();
        }

        $success = $this->pageModel->togglePerguntaStatus($perguntaId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit();
    }

    /**
     * Renderiza a view de adicionar pergunta
     */
    public function mostrarAdicionarPergunta()
    {
        if (! $this->isAdmin()) {
            $this->redirect('/login');
            return;
        }

        $this->render('pergunta', [
            'title'         => 'Adicionar Nova Pergunta',
            'areas'         => $this->userModel->getAreasAtuacao(),
            'niveis'        => $this->pageModel->getNiveisDificuldade(),
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser()['nome'],
            'usuarioLogado' => true,
        ]);
    }

    /**
     * Exibe um currículo específico
     */
    public function mostrarCurriculo()
    {
        $curriculoId = $_GET['id'] ?? null;

        if (! $curriculoId) {
            $this->renderError('ID do currículo não especificado');
            return;
        }

        $curriculo = $this->pageModel->getCurriculoPorId($curriculoId);

        if (! $curriculo) {
            $this->renderError('Currículo não encontrado');
            return;
        }

        $this->render('curriculo', [
            'title'         => 'Currículo de ' . htmlspecialchars($curriculo['nome']),
            'curriculo'     => $curriculo,
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser() ? $this->getCurrentUser()['nome'] : 'Visitante',
            'usuarioLogado' => $this->isLoggedIn(),
        ]);
    }

    // Métodos auxiliares

    private function getCurrentUser()
    {
        return isset($_SESSION['user_id']) ? $this->userModel->getUserById($_SESSION['user_id']) : null;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'footer.php';
    }

    private function renderError($message)
    {
        $this->render('error', [
            'title'         => 'Erro',
            'message'       => $message,
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser() ? $this->getCurrentUser()['nome'] : 'Visitante',
            'usuarioLogado' => $this->isLoggedIn(),
        ]);
    }

    private function redirect($url)
    {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit();
    }

}
