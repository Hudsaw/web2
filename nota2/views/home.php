<main class="container">
    <section class="hero card">
        <h1><?= $dados['titulo'] ?></h1>
        <p><?= $dados['descricao'] ?></p>
        <a href="<?= BASE_URL ?>?action=cadastro" class="btn">Cadastre-se</a>
    </section>

    <section class="card">
        <h2>Sobre o Quiz</h2>
        <p>O Quiz ADS é um teste de conhecimento para avaliar as habilidades e competências dos candidatos para as vagas de ADS.</p>
        <a href="<?= BASE_URL ?>?action=quiz" class="btn btn-entrar">Jogar</a>
        <a href="<?= BASE_URL ?>?action=adicionarPergunta" class="btn btn-entrar">Criar Pergunta</a>
        <?php if ($_SESSION['tipo_usuario'] == 'admin'): ?>
            <a href="<?= BASE_URL ?>?action=admin" class="btn btn-entrar">Gerenciar</a>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>Buscar Currículos</h2>
        <form method="get" action="<?= BASE_URL ?>" class="search-form">
            <input type="hidden" name="action" value="busca">
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
            <a href="<?= BASE_URL ?>?action=busca" class="btn btn-todos">Ver todos os currículos</a>
        </form>
    </section>

    <section class="card">
        <h2>O que oferecemos</h2>
        <ul>
            <li>✔ Banco de talentos qualificados</li>
            <li>✔ Parcerias com empresas líderes de mercado</li>
            <li>✔ Visibilidade real para profissionais de destaque</li>
        </ul>
    </section>
</main>