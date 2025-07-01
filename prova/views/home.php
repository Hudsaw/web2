<main class="container">
    <section class="hero card">
        <h1><?= $dados['titulo'] ?? 'Bem-vindo' ?></h1>
        <p><?= $dados['descricao'] ?? 'Sistema de avaliação de conhecimentos' ?></p>
        <?php if (isset($dados['usuario'])): ?>
            <a href="<?= BASE_URL ?>/cadastro" class="btn">Editar cadastro</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/cadastro" class="btn">Cadastre-se</a>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Sobre o Quiz</h2>
        <?php if (isset($dados['usuario']) && isset($dados['usuario']['avaliacao']) && isset($dados['usuario']['total_perguntas']) && $dados['usuario']['total_perguntas'] > 0): ?>
            <span class="user-score">
                <?= round(($dados['usuario']['avaliacao'] / $dados['usuario']['total_perguntas']) * 100) ?>% acertos
            </span>
        <?php elseif (isset($dados['usuario'])): ?>
            <span class="user-score">
                0% acertos
            </span>
        <?php endif; ?>
        <p>O Quiz ADS é um teste de conhecimento para avaliar as habilidades e competências dos candidatos para as vagas de ADS.</p>
        <div class="form-group">
            <a href="<?= BASE_URL ?>/quiz" class="btn btn-entrar">Jogar</a>
            <a href="<?= BASE_URL ?>/adicionarpergunta" class="btn btn-entrar">Criar Pergunta</a>
            <?php if (isset($dados['usuario']) && $dados['usuario']['tipo'] == 'admin'): ?>
                <a href="<?= BASE_URL ?>/admin" class="btn btn-entrar">Gerenciar</a>
            <?php endif; ?>
        </div>
    </section>

    <section class="card">
        <h2>Buscar Currículos</h2>
        <form method="get" action="<?= BASE_URL ?>/busca" class="search-form">
            <div class="form-group">
                <label for="area">Filtrar por Área:</label>
                <select name="area" id="area">
                    <option value="">Todas as áreas</option>
                    <?php if (!empty($areas)): ?>
                        <?php foreach ($areas as $area): ?>
                            <option value="<?= $area['id'] ?>">
                                <?= htmlspecialchars($area['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-buscar">Filtrar</button>
            <a href="<?= BASE_URL ?>/busca" class="btn btn-todos">Ver todos os currículos</a>
        </form>
    </section>
</main>