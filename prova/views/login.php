<div class="container">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h1 class="text-center">Acesse sua conta</h1>
        
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
        </div>
    </div>
</div>