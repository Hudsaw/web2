<?php
require_once 'config.php';
require_once 'Controller.php';

// Roteamento básico
$action = $_GET['action'] ?? 'home';

try {
    $controller = new MainController();

    switch ($action) {
        case 'home':
            $controller->home();
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
                require_once VIEWS_PATH.'login.php';
            }
            break;

        case 'cadastro':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->processarCadastro();
            } else {
                require_once VIEWS_PATH.'cadastro.php';
            }
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            require_once VIEWS_PATH.'404.php';
    }
} catch (Exception $e) {
    // Tratamento de erros
    error_log($e->getMessage());
    header("HTTP/1.1 500 Internal Server Error");
    require_once 'erro.php';
}
