<?php
require_once 'constants.php';
require_once 'controller.php';

// Inicia a sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Roteamento básico
$action = $_GET['action'] ?? 'home';

try {
    $controller = new MainController();

    // Verifica se a ação requer autenticação
    $protectedActions = ['admin', 'adicionarPergunta', 'editarPergunta'];
    if (in_array($action, $protectedActions) && ($_SESSION['tipo_usuario'] ?? '') != 'admin') {
        header("Location: " . BASE_URL . "?action=login");
        exit();
    }
    error_log("======== NOVA REQUISICAO ========");
    error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
    error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    error_log("POST: " . print_r($_POST, true));
    error_log("GET: " . print_r($_GET, true));
    error_log("action: " . $action);

    switch ($action) {
        case 'home':
            $controller->home();
            break;
        case 'busca':
            $controller->buscarCurriculos();
            break;
        case 'curriculo':
            $id = $_GET['id'] ?? 0;
            $controller->verCurriculo($id);
            break;
        case 'atualizar':
            $controller->processarAtualizacao();
            break;
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->processarLogin();
            } else {
                $controller->mostrarLogin();
            }
            break;
        case 'cadastro':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->processarCadastro();
            } else {
                $controller->mostrarCadastro();
            }
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'quiz':
            $controller->mostrarQuiz();
            break;
        case 'jogar':
            $controller->iniciarQuiz();
            break;
        case 'processarQuiz':
            $controller->processarQuiz();
            break;
        case 'admin':
            $controller->gerenciar();
            break;
        case 'toggleStatus':
            $controller->toggleStatus();
            break;
        case 'excluirPergunta':
            $controller->excluirPergunta();
            break;
        case 'adicionarPergunta':
            $controller->mostrarFormularioPergunta();
            break;
        case 'cadastrarPergunta':
            $controller->processarAdicaoPergunta();
            break;
        default:
            header("HTTP/1.0 404 Not Found");
            require_once VIEWS_PATH . '404.php';
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");

    $dados = [
        'titulo'    => 'Erro no Sistema',
        'descricao' => 'Desculpe, ocorreu um erro inesperado. Nossa equipe foi notificada.',
        'areas'     => [],
    ];

    require VIEWS_PATH . 'header.php';
    require VIEWS_PATH . 'erro.php';
    require VIEWS_PATH . 'footer.php';
}
