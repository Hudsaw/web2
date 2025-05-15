<?php
require_once __DIR__ . '/database.php';

// Inicia a sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Valores padrão
$nomeUsuario = 'Visitante';
$usuarioLogado = false;

// Verifica se o usuário está logado
if (isset($_SESSION['id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['nome'] ?? 'Usuário';

    // Conexão com o banco para notificações
    try {
        $pdo = Database::getInstance();
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
    <title>Curriculum Premium</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="header">
        <div class="header-container">
    
            <div class="logo">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <img src="https://img.icons8.com/?size=100&id=45588&format=png&color=FFFFFF" alt="Logo" class="logo-img" />
                    <span class="logo-text">Curriculum Premium</span>
                </a>
            </div>

            <nav class="nav-user">

                <div class="user-greeting">
                    <span>Olá, <?php echo htmlspecialchars($nomeUsuario); ?>!</span>
                </div>
                
                <?php if ($usuarioLogado): ?>
                    <a href="<?php echo BASE_URL;?>AuthController.php?action=logout" class="btn-logout">
                        <span class="btn-text">Sair</span>
                    </a>

                <?php else: ?>
                    <a href="<?php echo BASE_URL;?>login.php" class="btn-login">
                        <span class="btn-text">Login</span>
                    </a>
                <?php endif; ?>


            </nav>
        </div>
    </header>
    <main>
        
    <?php require_once __DIR__ . '/rodape.php'; ?>