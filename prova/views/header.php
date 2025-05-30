<?php
require_once 'constants.php';

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
    <header>
        <div class="container">
            <div class="header-container">
                <nav class="secao">
                    <div class="logo">
                        <div>
                            <img src="https://img.icons8.com/?size=100&id=45588&format=png&color=FFFFFF" alt="Logo" class="logo-img" />
                        </div>
                        <div id="espacador">.</div>
                        <div>
                <a href="<?= BASE_URL ?>" class="logo">Curriculum Premium</a>
                        </div>
                    </div>
                    </a>

                    <nav class="nav-user">
                        <div class="user-greeting">
                            <span>Olá, <?= htmlspecialchars($nomeUsuario) ?>!</span>
                        </div>

                        <?php if ($usuarioLogado): ?>
                            <a href="<?= BASE_URL ?>?action=logout" class="btn-logout">
                                <span class="btn-text">Sair</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>?action=login" class="btn-login">
                                <span class="btn-text">Login</span>
                            </a>
                            </a>
                        <?php endif; ?>
                    </nav>
            </div>
    </header>
