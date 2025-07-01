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
    $user = $this->getCurrentUser();
    
    $data = [
        'title' => 'Bem-vindo',
        'areas' => $this->userModel->getAreasAtuacao(),
        'user' => $user,
        // Variáveis para o header
        'nomeUsuario' => $user ? $user['nome'] : 'Visitante',
        'usuarioLogado' => $this->isLoggedIn(),
        // Variáveis para o home.php
        'dados' => [
            'titulo' => 'Bem-vindo ao Curriculum Quiz',
            'descricao' => 'Sistema de avaliação de conhecimentos para profissionais de ADS',
            'usuario' => $user
        ]
    ];
    
    $this->render('home', $data);
}

    public function buscarCurriculos()
    {
        $areaFilter = $_GET['area'] ?? null;
        $page = max((int) ($_GET['page'] ?? 1), 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $results = $this->pageModel->getCurriculos($areaFilter, $perPage, $offset);
        $total = $this->pageModel->countCurriculos($areaFilter);

        $data = [
            'title' => 'Busca de Currículos',
            'results' => $results,
            'areas' => $this->userModel->getAreasAtuacao(),
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'areaFilter' => $areaFilter,
            'user' => $this->getCurrentUser()
        ];
        
        $this->render('search', $data);
    }

    public function mostrarQuiz()
    {
        $data = [
            'title' => 'Quiz',
            'areas' => $this->userModel->getAreasAtuacao(),
            'user' => $this->getCurrentUser()
        ];
        
        $this->render('quiz', $data);
    }

    public function iniciarQuiz()
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_url'] = '?action=startQuiz';
            $this->redirect('/login');
        }

        $areaId = $_GET['area'] ?? null;
        if (!$areaId) {
            $this->renderError('Área não especificada');
            return;
        }

        $questions = $this->pageModel->getPerguntasAleatorias($areaId);

        if (count($questions) < 5) {
            $this->renderError('Não há perguntas suficientes para esta área');
            return;
        }

        $data = [
            'title' => 'Quiz em Andamento',
            'questions' => $questions,
            'areaId' => $areaId,
            'user' => $this->getCurrentUser()
        ];

        $this->render('quiz', $data);
    }

    public function processQuiz()
    {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['answers']) || !isset($data['questionIds'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit();
        }

        $correct = 0;
        for ($i = 0; $i < count($data['questionIds']); $i++) {
            if ($this->pageModel->verificarResposta($data['questionIds'][$i], $data['answers'][$i])) {
                $correct++;
            }
        }

        $percentage = round(($correct / count($data['questionIds'])) * 100);
        $userId = $_SESSION['user_id'];

        $this->pageModel->updateUserScore($userId, $correct, count($data['questionIds']));

        echo json_encode([
            'success' => true,
            'correct' => $correct,
            'percentage' => $percentage,
            'redirect' => BASE_URL
        ]);
    }

    public function gerenciarQuiz()
    {
        if (!$this->isAdmin()) {
            $this->redirect('/login');
        }

        $page = max((int) ($_GET['page'] ?? 1), 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $data = [
            'title' => 'Painel Admin',
            'questions' => $this->pageModel->getPerguntasPaginadas($perPage, $offset),
            'currentPage' => $page,
            'totalPages' => ceil($this->pageModel->countPerguntas() / $perPage),
            'user' => $this->getCurrentUser()
        ];

        $this->render('admin', $data);
    }

    public function adicionarPergunta()
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            exit();
        }

        $dados = [
            'pergunta' => $_POST['pergunta'],
            'resposta_correta' => $_POST['resposta_correta'],
            'alternativa1' => $_POST['alternativa1'],
            'alternativa2' => $_POST['alternativa2'],
            'alternativa3' => $_POST['alternativa3'],
            'area_atuacao_id' => $_POST['area_atuacao_id'],
            'nivel_id' => $_POST['nivel_id']
        ];

        $success = $this->pageModel->addPergunta($dados);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function excluirPergunta()
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            exit();
        }

        $perguntaId = $_POST['id'] ?? null;
        if (!$perguntaId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da pergunta não fornecido']);
            exit();
        }

        $success = $this->pageModel->deletePergunta($perguntaId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

    public function toggleStatus()
    {
        if (!$this->isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acesso negado']);
            exit();
        }

        $perguntaId = $_POST['id'] ?? null;
        if (!$perguntaId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID da pergunta não fornecido']);
            exit();
        }

        $success = $this->pageModel->togglePerguntaStatus($perguntaId);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
    }

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
    // Adiciona variáveis padrão necessárias para o header
    $defaultData = [
        'nomeUsuario' => 'Visitante',
        'usuarioLogado' => false
    ];
    
    // Combina os dados específicos com os padrões
    $data = array_merge($defaultData, $data);
    
    extract($data);
    require VIEWS_PATH . 'header.php';
    require VIEWS_PATH . $view . '.php';
    require VIEWS_PATH . 'footer.php';
}

    private function renderError($message)
    {
        $data = [
            'title' => 'Erro',
            'message' => $message,
            'user' => $this->getCurrentUser()
        ];
        $this->render('error', $data);
    }

    private function redirect($url)
    {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit();
    }
}