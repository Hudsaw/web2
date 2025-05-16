<main class="apresentacao">
    <section class="hero">
        <h1><?= $dados['titulo'] ?></h1>
        <p><?= $dados['descricao'] ?></p>
        <a href="<?= BASE_URL ?>?action=cadastro" class="btn">Cadastre seu currículo</a>
    </section>

    <section class="busca-curriculos">
        <h2>Buscar Currículos por Área de Atuação</h2>
        <form method="get" action="<?= BASE_URL ?>">
            <input type="hidden" name="action" value="buscar">
            <div class="form-group">
                <label for="area">Área:</label>
                <select name="area" id="area" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($this->model->getAreasAtuacao() as $area): ?>
                        <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-buscar">Buscar</button>
        </form>
    </section>

    <section class="vantagens">
        <h2>O que oferecemos</h2>
        <ul>
            <li>✔ Banco de talentos qualificados</li>
            <li>✔ Parcerias com empresas líderes de mercado</li>
            <li>✔ Visibilidade real para profissionais de destaque</li>
        </ul>
    </section>
</main>