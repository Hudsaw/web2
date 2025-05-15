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
if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $nomeUsuario = $_SESSION['usuario_nome'] ?? 'Usuário';

    // Conexão com o banco para notificações
    try {
        $pdo = Database::getInstance();
        $stmt->execute([$_SESSION['usuario_id']]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
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
    
                <span class="logo-text">Curriculum Premium</span>

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
                        <i class="fas fa-sign-in-alt"></i> <span class="btn-text">Login</span>
                    </a>
                <?php endif; ?>


            </nav>
        </div>
    </header>
    <main>
        
    <?php require_once __DIR__ . '/rodape.php'; ?>