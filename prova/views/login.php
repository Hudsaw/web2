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
        <a href="<?= BASE_URL ?>?action=cadastro">
            <i class="fas fa-user-plus"></i> Criar nova conta
        </a>
    </div>
</div>