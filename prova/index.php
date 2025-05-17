<?php
require_once 'constants.php';
require_once 'Controller.php';

// Roteamento básico
$action = $_GET['action'] ?? 'home';

try {
    $controller = new MainController();

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

        default:
            header("HTTP/1.0 404 Not Found");
            require_once VIEWS_PATH . '404.php';
    }
} catch (Exception $e) {
    // Tratamento de erros
    error_log($e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    require_once 'erro.php';
}
