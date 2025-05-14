<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/database.php';

use Config\Database;

// Inicia sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,
        'cookie_httponly' => true
    ]);
}

// Redireciona se já logado
if (isset($_SESSION['usuario_id'])) {
    
    // Redirecionamento seguro
    $redirect = match($usuario['tipo']) {
        'admin' => 'views/admin/dashboard.php',
        'medico' => 'views/medico/dashboard.php',
        'especialista' => 'views/especialista/dashboard.php',
        default => 'public/index.php'
    };
    
    header('Location: ' . BASE_URL . $redirect);
    exit();
    
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'])); 
    $senha = $_POST['senha'];
    
    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, nome, senha, tipo FROM usuarios WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                // Regenera o ID da sessão para prevenir fixation
                session_regenerate_id(true);
                
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['tipo_usuario'] = $usuario['tipo'];
                
                // Redirecionamento seguro
                $redirect = match($usuario['tipo']) {
                    'admin' => 'views/admin/dashboard.php',
                    'medico' => 'views/medico/dashboard.php',
                    'especialista' => 'views/especialista/dashboard.php',
                    default => 'public/index.php'
                };
                
                header('Location: ' . BASE_URL . $redirect);
                exit();
            } else {
                $erro = "Credenciais inválidas"; 
            }
        } else {
            $erro = "Credenciais inválidas"; 
        }
    } catch (PDOException $e) {
        error_log("Erro de login: " . $e->getMessage());
        $erro = "Sistema indisponível. Tente mais tarde.";
    }
}

require_once VIEWS_PATH . 'shared/header.php';
?>

<div class="auth-container">
    <h1 class="auth-title">Acesse sua conta</h1>
    
    <?php if ($erro): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($erro); ?>
        </div>
    <?php endif; ?>
    
    <form class="auth-form" method="POST">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" required 
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <button type="submit" class="btn btn-primary">
    <i class="fas fa-sign-in-alt"></i> Entrar
</button>
    </form>
    <div class="auth-links">
        <a href="<?php echo BASE_URL; ?>views/auth/resetar.php">
            <i class="fas fa-key"></i> Esqueceu sua senha?
        </a>
        <span> | </span>
        <a href="<?php echo BASE_URL; ?>views/auth/cadastro.php">
            <i class="fas fa-user-plus"></i> Criar nova conta
        </a>
    </div>
</div>

<?php 
require_once VIEWS_PATH . 'shared/footer.php';
?>