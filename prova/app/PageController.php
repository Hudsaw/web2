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

    public function home()
    {
        error_log("Exibindo pagina inicial");
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
        error_log("Exibindo formulario de cadastro");
        $data = [
            'errors' => $_SESSION['register_errors'] ?? [],
            'dados'  => [],
            'areas'  => $this->userModel->getAreasAtuacao(),
        ];

        // Se usuário está logado, carrega seus dados
        if (isset($_SESSION['user_id'])) {
            $data['dados'] = $this->userModel->getUserById($_SESSION['user_id']);
        }

        $this->render('cadastro', $data);
        unset($_SESSION['register_errors']);
    }

    public function buscarCurriculos()
    {
        error_log("Buscando curriculos com filtro de area");
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

    public function mostrarQuiz()
    {
        error_log("Exibindo pagina de selecao de quiz");
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

    public function iniciarQuiz()
    {
        error_log("Iniciando quiz");
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

    public function finalizarQuiz()
    {
        error_log("Finalizando quiz");
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

    public function gerenciarQuiz()
    {
        error_log("Tentativa de acesso ao painel administrativo");
        if (! $this->isAdmin()) {
            $this->redirect('/login');
            return;
        }

        $paginaAtual = max((int) ($_GET['page'] ?? $_GET['pagina'] ?? 1), 1);
        $perPage     = 10;
        $offset      = ($paginaAtual - 1) * $perPage;

        $perguntas      = $this->pageModel->getPerguntasPaginadas($perPage, $offset);
        $totalPerguntas = $this->pageModel->countPerguntas();

        $this->render('admin', [
            'titulo'         => 'Painel Administrativo',
            'perguntas'      => $perguntas,
            'paginaAtual'    => $paginaAtual,
            'totalPerguntas' => $totalPerguntas,
            'totalPaginas'   => ceil($totalPerguntas / $perPage),
            'user'           => $this->getCurrentUser(),
            'nomeUsuario'    => $this->getCurrentUser()['nome'],
            'usuarioLogado'  => true,
        ]);
    }

    public function adicionarPergunta()
    {
        error_log("Tentativa de adicionar pergunta");
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

        $_SESSION['mensagem'] = $success ? 'Pergunta adicionada com sucesso!' : 'Erro ao excluir pergunta';
        $this->redirect('/');
    }

    public function excluirPergunta()
    {
        error_log("Tentativa de exclusao de pergunta");
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

        $_SESSION['mensagem'] = $success ? 'Pergunta excluída com sucesso!' : 'Erro ao excluir pergunta';
        $this->redirect('/admin?page=' . $paginaAtual);
    }

    public function toggleStatus()
    {
        error_log("Tentativa de alternar status da pergunta");
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

        $_SESSION['mensagem'] = $success ? 'Status da pergunta alterado com sucesso!' : 'Erro ao alterar status';
        $this->redirect('/admin?page=' . $paginaAtual);
    }

    public function mostrarAdicionar()
    {
        error_log("Exibindo formulario de adicao de pergunta");
        $this->render('pergunta', [
            'titulo'        => 'Adicionar Nova Pergunta',
            'areas'         => $this->userModel->getAreasAtuacao(),
            'niveis'        => $this->pageModel->getNiveisDificuldade(),
            'user'          => $this->getCurrentUser(),
            'nomeUsuario'   => $this->getCurrentUser()['nome'] ?? 'Visitante',
            'usuarioLogado' => true,
        ]);
    }

    public function mostrarCurriculo()
    {
        error_log("Exibindo curriculo");
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
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $user = $this->userModel->getUserById($_SESSION['user_id']);
    
    // Adicionar pontuação se não estiver presente
    if ($user && !isset($user['avaliacao'])) {
        $score = $this->userModel->getUserScore($_SESSION['user_id']);
        $user['avaliacao'] = $score['correct'] ?? 0;
        $user['total_perguntas'] = $score['total'] ?? 0;
    }
    
    return $user;
}

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function isAdmin()
    {
        error_log("Verificando se usuário é administrador" . $_SESSION['user_role']);
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
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
        exit();
    }

}
