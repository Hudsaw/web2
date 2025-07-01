<?php
// Configurações globais
define('BASE_PATH', 'C:\xampp\htdocs\web2\prova');
define('BASE_URL', 'https://localhost/web2/prova');
define('VIEWS_PATH', __DIR__ . '/views/');
define('DB_HOST', 'localhost');
define('DB_NAME', 'quizads');
define('DB_USER', 'root');
define('DB_PASS', '');


// Inicia a sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => false, 
        'cookie_httponly' => true,
        'use_strict_mode' => true
    ]);
}

// Funções úteis
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}