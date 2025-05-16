<?php
require_once 'config.php';

$model = new Model();
$usuarioLogado = isset($_SESSION['id']);
$nomeUsuario = $usuarioLogado ? ($_SESSION['nome'] ?? 'Usuário') : 'Visitante';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>style.css">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="<?= BASE_URL ?>" class="logo">
                    <img src="https://img.icons8.com/?size=100&id=45588&format=png&color=FFFFFF" alt="Logo" class="logo-img" />
                    <span class="logo-text">Curriculum Premium</span>
                </a>
            </div>

            <nav class="nav-user">
                <div class="user-greeting">
                    <span>Olá, <?= htmlspecialchars($nomeUsuario) ?>!</span>
                </div>
                
                <?php if ($usuarioLogado): ?>
                    <a href="<?= BASE_URL ?>?action=logout" class="btn-logout">
                        <span class="btn-text">Sair</span>
                    </a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>login.php" class="btn-login">
                        <span class="btn-text">Login</span>
                    </a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main>