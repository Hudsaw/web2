<main class="container">
    <section class="card">
        <h1><?= $titulo ?></h1>
        
        <?php if (isset($_SESSION['erro'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['erro'] ?></div>
            <?php unset($_SESSION['erro']); ?>
        <?php endif; ?>
        
        <form id="formPergunta" method="POST" action="<?= BASE_URL ?>/adicionar">
            <!-- Campos do formulário -->
            <div class="form-group">
                <label for="pergunta">Pergunta:</label>
                <textarea id="pergunta" name="pergunta" required><?= $_SESSION['dados_form']['pergunta'] ?? '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="resposta_correta">Resposta Correta:</label>
                <input type="text" id="resposta_correta" name="resposta_correta" 
                       value="<?= $_SESSION['dados_form']['resposta_correta'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alternativa1">Alternativa 1:</label>
                <input type="text" id="alternativa1" name="alternativa1" 
                       value="<?= $_SESSION['dados_form']['alternativa1'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alternativa2">Alternativa 2:</label>
                <input type="text" id="alternativa2" name="alternativa2" 
                       value="<?= $_SESSION['dados_form']['alternativa2'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="alternativa3">Alternativa 3:</label>
                <input type="text" id="alternativa3" name="alternativa3" 
                       value="<?= $_SESSION['dados_form']['alternativa3'] ?? '' ?>" required>
            </div>
            
            <div class="form-group">
                <label for="area_atuacao_id">Área de Atuação:</label>
                <select id="area_atuacao_id" name="area_atuacao_id" required>
                    <option value="">Selecione uma área</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?= $area['id'] ?>" 
                            <?= isset($_SESSION['dados_form']['area_atuacao_id']) && $_SESSION['dados_form']['area_atuacao_id'] == $area['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($area['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="nivel_id">Nível de Dificuldade:</label>
                <select id="nivel_id" name="nivel_id" required>
                    <?php foreach ($niveis as $nivel): ?>
                        <option value="<?= $nivel['id'] ?>" 
                            <?= isset($_SESSION['dados_form']['nivel_id']) && $_SESSION['dados_form']['nivel_id'] == $nivel['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nivel['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn">Salvar Pergunta</button>
        </form>
    </section>
</main>
<script>
document.getElementById('formPergunta').addEventListener('submit', function(e) {
    if (!window.fetch) {
        return; 
    }
    
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = this.action;
    
    fetch(action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else if (!response.ok) {
            throw new Error('Erro na requisição');
        }
        return response.text();
    })
    .then(data => {
        if (data) {
            document.body.innerHTML = data;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Recarrega a página em caso de erro
        window.location.reload();
    });
});
</script>