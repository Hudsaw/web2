<main class="container">
    <section class="card">
        <h1><?= $dados['titulo'] ?></h1>
        <p><?= $dados['descricao'] ?></p>
        <h2>Selecione uma Área:</h2>
        <form method="get" action="<?= BASE_URL ?>/jogar">
            <select name="area" required>
                <option value="" disabled selected>Escolha uma área</option>
                <?php foreach ($areas as $area): ?>
                    <option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['nome']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn">Jogar</button>
        </form>
    </section>
</main>