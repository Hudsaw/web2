<?php
//views/shared/header.php

use Config\Database;

// Inicia a sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valores padrão
$nomeUsuario = 'Visitante';
$usuarioLogado = false;
$notificacoesNaoLidas = 0;

// Verifica se o usuário está logado
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['usuario_nome'] ?? 'Usuário';

    // Conexão com o banco para notificações
    require_once __DIR__ . '/../../config/database.php';
    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notificacoes 
                              WHERE usuario_id = ? AND lida = 0");
        $stmt->execute([$_SESSION['usuario_id']]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $notificacoesNaoLidas = $resultado['total'];
    } catch (PDOException $e) {
        error_log("Erro ao buscar notificações: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parecer Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/views.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/tables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        // Verifica preferência salva ou do sistema
        function getThemePreference() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) return savedTheme;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        // Aplica o tema
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateToggleButton(theme);
        }

        // Atualiza o botão toggle
        function updateToggleButton(theme) {
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) {
                toggleBtn.innerHTML = theme === 'dark' ?
                    '<i class="fas fa-sun"></i>' :
                    '<i class="fas fa-moon"></i>';
            }
        }

        // Carrega o tema ao iniciar
        document.addEventListener('DOMContentLoaded', () => {
            const currentTheme = getThemePreference();
            applyTheme(currentTheme);
        });
    </script>
</head>

<body>
    <header class="header">
        <div class="header-container">
            <a href="<?php echo PUBLIC_URL; ?>" class="logo">
                <i class="fas fa-hospital-alt"></i> <span class="logo-text">Parecer Digital</span>
            </a>
            <nav class="nav-user">

                <div class="user-greeting">
                    <span>Olá, <?php echo htmlspecialchars($nomeUsuario); ?>!</span>
                    <?php if ($usuarioLogado): ?>
                        <a href="<?php echo BASE_URL; ?>notificacoes" class="btn-dashboard">
                            <i class="fas fa-bell"></i>
                            <?php if ($notificacoesNaoLidas > 0): ?>
                                <span class="badge"><?php echo $notificacoesNaoLidas; ?></span>
                            <?php endif; ?>
                        </a>

                        <?php if ($_SESSION['tipo_usuario'] === 'admin'): ?>
                            <a href="<?php echo BASE_URL; ?>views/admin/dashboard.php" class="btn-dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                            </a>
                        <?php elseif ($_SESSION['tipo_usuario'] === 'medico'): ?>
                            <a href="<?php echo BASE_URL; ?>views/medico/dashboard.php" class="btn-dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                            </a>
                        <?php elseif ($_SESSION['tipo_usuario'] === 'especialista'): ?>
                            <a href="<?php echo BASE_URL; ?>views/especialista/dashboard.php" class="btn-dashboard">
                                <i class="fas fa-tachometer-alt"></i>
                            </a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <!-- Botão de Toggle do Tema -->
                <button id="theme-toggle" class="btn-dashboard">
                    <i class="fas fa-moon"></i>
                </button>
                <?php if ($usuarioLogado): ?>
                    <a href="<?php echo BASE_URL; ?>app/controllers/AuthController.php?action=logout" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> <span class="btn-text">Sair</span>
                    </a>

                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>views/auth/login.php" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> <span class="btn-text">Login</span>
                    </a>
                <?php endif; ?>


            </nav>
        </div>
    </header>
    <main>

        <script>
            // Adiciona evento ao botão de toggle
            document.getElementById('theme-toggle').addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                applyTheme(newTheme);
            });
        </script>