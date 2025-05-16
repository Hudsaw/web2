<div class="apresentacao">
    <section id="pessoais">
        <h1><?= htmlspecialchars($curriculo['nome'] ?? '') ?></h1>
        <?php if (!empty($curriculo['area'])): ?>
            <p><strong>Área:</strong> <?= htmlspecialchars($curriculo['area']) ?></p>
        <?php endif; ?>
        <p><strong>E-mail:</strong> <?= htmlspecialchars($curriculo['email']) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($curriculo['telefone']) ?></p>
        <?php if (!empty($curriculo['resumo'])): ?>
            <p><strong>Resumo:</strong> <?= nl2br(htmlspecialchars($curriculo['resumo'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($curriculo['escolaridade'])): ?>
            <p><strong>Escolaridade:</strong> <?= ucfirst(htmlspecialchars($curriculo['escolaridade'])) ?></p>
        <?php endif; ?>
        <?php if (!empty($curriculo['experiencias'])): ?>
            <p><strong>Experiências:</strong> <?= htmlspecialchars($curriculo['experiencias']) ?></p>
        <?php endif; ?>
    </section>
</div>