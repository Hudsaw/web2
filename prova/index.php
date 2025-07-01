<?php
require_once 'constants.php';
require_once __DIR__.'/app/Database.php';
require_once __DIR__.'/app/UserModel.php';
require_once __DIR__.'/app/PageModel.php';
require_once __DIR__.'/app/PageController.php';
require_once __DIR__.'/app/AuthController.php';
require_once __DIR__.'/app/AuthMiddleware.php';
require_once __DIR__.'/app/Container.php';
require_once __DIR__.'/app/Router.php';

// Inicializa o container de dependências
$container = new Container();

// Configura o roteador
$router = new Router($container);

// Rotas principais
$router->get('/', 'PageController@home');
$router->get('/busca', 'PageController@buscarCurriculos');
$router->get('/curriculo', 'PageController@mostrarCurriculo');

// Rotas de autenticação
$router->get('/login', 'AuthController@showLogin');
$router->get('/cadastro', 'AuthController@showRegister');
$router->get('/logout', 'AuthController@logout');
$router->post('/login', 'AuthController@login');
$router->post('/cadastro', 'AuthController@register');

// Rotas do quiz
$router->get('/quiz', 'PageController@mostrarQuiz');
$router->get('/jogar', 'PageController@iniciarQuiz');
$router->post('/finalizar', 'PageController@finalizarQuiz');

// Rotas administrativas (protegidas)
$router->get('/admin', 'PageController@gerenciarQuiz', ['AuthMiddleware']);
$router->post('/adicionar', 'PageController@adicionarPergunta', ['AuthMiddleware']);
$router->post('/excluir', 'PageController@excluirPergunta', ['AuthMiddleware']);
$router->post('/toggleStatus', 'PageController@toggleStatus', ['AuthMiddleware']);

$router->dispatch();