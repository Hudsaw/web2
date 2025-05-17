php
<div class="container">
    <div class="card">
        <div class="curriculo-header">
            <h1><?= htmlspecialchars($curriculo['nome'] ?? 'Nome não disponível') ?></h1>
            <section id="pessoais">

                <?php if (!empty($curriculo['area_nome'])): ?>
                    <p><strong>Área:</strong> <?= htmlspecialchars($curriculo['area_nome']) ?></p>
                <?php endif; ?>

                <p><strong>E-mail:</strong> <?= htmlspecialchars($curriculo['email'] ?? 'Não informado') ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($curriculo['telefone'] ?? 'Não informado') ?></p>

                <?php if (!empty($curriculo['resumo'])): ?>
                    <div class="resumo">
                        <h3>Resumo Profissional</h3>
                        <p><?= nl2br(htmlspecialchars($curriculo['resumo'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($curriculo['escolaridade'])): ?>
                    <div class="escolaridade">
                        <h3>Escolaridade</h3>
                        <p><?= ucfirst(htmlspecialchars($curriculo['escolaridade'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($curriculo['experiencias'])): ?>
                    <div class="experiencias">
                        <h3>Experiências Profissionais</h3>
                        <p><?= nl2br(htmlspecialchars($curriculo['experiencias'])) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($curriculo['linkedin']) || !empty($curriculo['github'])): ?>
                    <div class="redes-sociais">
                        <h3>Redes Sociais</h3>
                        <?php if (!empty($curriculo['linkedin'])): ?>
                            <p><a href="<?= htmlspecialchars($curriculo['linkedin']) ?>" target="_blank">LinkedIn</a></p>
                        <?php endif; ?>
                        <?php if (!empty($curriculo['github'])): ?>
                            <p><a href="<?= htmlspecialchars($curriculo['github']) ?>" target="_blank">GitHub</a></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
            <?php if (!empty($curriculo['editar'])): ?>
                <div class="text-right">
                    <a href="<?= BASE_URL ?>?action=cadastro" class="btn">
                        Editar Currículo
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>