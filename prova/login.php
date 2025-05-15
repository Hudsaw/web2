<?php
require_once __DIR__ . '/database.php';

// Inicia sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,
        'cookie_httponly' => true
    ]);
}

$erro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM curriculo WHERE LOWER(email) = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            error_log("Hash armazenado: " . $usuario['senha']);
            error_log("Senha fornecida: " . $senha);
            if (strlen($usuario['senha']) === 64 && ctype_xdigit($usuario['senha'])) {
                $isValid = hash('sha256', $senha) === $usuario['senha'];
            } else {
                // Caso contrário, usa password_verify para BCrypt
                $isValid = password_verify($senha, $usuario['senha']);
            }

            if ($isValid) {
                // Regenera o ID da sessão para prevenir fixation
                session_regenerate_id(true);
                error_log("Senha verificada com sucesso");

                $_SESSION['id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];

                // Define um redirecionamento padrão seguro
                $redirect = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : BASE_URL . 'index.php';
                unset($_SESSION['redirect_url']);

                header('Location: ' . $redirect);
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

// Inclui o header.php
require_once __DIR__ . '/header.php';
?>

<div class="apresentacao">
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
        <a href="<?php echo BASE_URL; ?>resetar.php">
            <i class="fas fa-key"></i> Esqueceu sua senha?
        </a>
        <span> | </span>
        <a href="<?php echo BASE_URL; ?>cadastro.php">
            <i class="fas fa-user-plus"></i> Criar nova conta
        </a>
    </div>
</div>

<?php
// Inclui o rodape.php
require_once __DIR__ . '/rodape.php';
?>