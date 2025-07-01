<main class="container">
    <section class="card">
        <h1>Quiz: <?= htmlspecialchars($titulo) ?></h1>
        
        <form id="quiz-form" method="post" action="<?= BASE_URL ?>/resultado">
            <input type="hidden" name="area_id" value="<?= $areaId ?>">
            <input type="hidden" name="nivel" value="<?= $nivel ?>">
            
            <?php foreach ($perguntas as $index => $pergunta): ?>
                <div class="pergunta">
                    <h3>Pergunta <?= $index + 1 ?>: <?= htmlspecialchars($pergunta['pergunta']) ?></h3>
                    
                    <div class="alternativas">
                        <?php 
                        $alternativas = [
                            $pergunta['resposta_correta'],
                            $pergunta['alternativa1'],
                            $pergunta['alternativa2'],
                            $pergunta['alternativa3']
                        ];
                        shuffle($alternativas);
                        ?>
                        
                        <?php foreach ($alternativas as $key => $alternativa): ?>
                            <label>
                                <input type="radio" 
                                       name="respostas[<?= $pergunta['id'] ?>]" 
                                       value=" <?= htmlspecialchars($alternativa) ?>">
                                       &nbsp;<?= htmlspecialchars($alternativa) ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <button type="submit" class="btn">Enviar Respostas</button>
        </form>
    </section>
</main>