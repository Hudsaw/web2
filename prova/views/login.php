<div class="apresentacao">
    <div class="auth-container">
        <h1 class="auth-title">Acesse sua conta</h1>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form class="auth-form" method="POST" action="<?= BASE_URL ?>?action=login">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <button type="submit" class="btn btn-primary">
                Entrar
            </button>
        </form>
        
        <div class="auth-links">
            <a href="<?= BASE_URL ?>?action=cadastro">
                Criar nova conta
            </a>
            <a href="<?= BASE_URL ?>?action=recuperar-senha" class="forgot-password">
                Esqueceu sua senha?
            </a>
        </div>
    </div>
</div>