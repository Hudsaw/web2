<main class="container">
    <section class="card">
        <?php if (isset($curriculo)): ?>
            <h1>Currículo de <?= htmlspecialchars($curriculo['nome']) ?></h1>
            
            <div class="curriculo-detalhes">
                <h2>Informações Pessoais</h2>
                <p><strong>Nome:</strong> <?= htmlspecialchars($curriculo['nome']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($curriculo['email']) ?></p>
                <p><strong>Telefone:</strong> <?= htmlspecialchars($curriculo['telefone'] ?? 'Não informado') ?></p>
                
                <h2>Área de Atuação</h2>
                <p><?= htmlspecialchars($curriculo['area_nome'] ?? 'Não informada') ?></p>
                
                <h2>Experiência Profissional</h2>
                <p><?= nl2br(htmlspecialchars($curriculo['experiencia'] ?? 'Não informada')) ?></p>
                
                <h2>Formação Acadêmica</h2>
                <p><?= nl2br(htmlspecialchars($curriculo['formacao'] ?? 'Não informada')) ?></p>
                
                <h2>Pontuação no Quiz</h2>
                <p><?= isset($curriculo['avaliacao']) ? $curriculo['avaliacao'] . '%' : 'Ainda não avaliado' ?></p>
            </div>
            
            <a href="<?= BASE_URL ?>/busca" class="btn">Voltar para a busca</a>
        <?php else: ?>
            <p class="error">Currículo não encontrado.</p>
        <?php endif; ?>
    </section>
</main>