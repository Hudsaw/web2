<main class="container">
    <section class="card">
        <h1><?php echo $dados['titulo']?></h1>

        <div id="quiz-container">
        <div id="quiz-progress">
    Pergunta <span id="pergunta-numero">1</span> de <?php echo isset($dados['totalPerguntas']) ? $dados['totalPerguntas'] : 0?>
</div>

            <div id="pergunta-area">
                <h3 id="pergunta-texto"></h3>
                <div id="alternativas-container" class="alternativas">
                    <!-- Alternativas serão preenchidas via JavaScript -->
                </div>
                <button id="confirmar-resposta" class="btn" disabled>Confirmar Resposta</button>
            </div>

            <div id="feedback-area" style="display: none;">
                <p id="feedback-mensagem"></p>
                <button id="proxima-pergunta" class="btn">Próxima Pergunta</button>
            </div>

            <div id="resultado-final" style="display: none;">
    <h3>Quiz Concluído!</h3>
    <p id="resultado-texto"></p>
    <div class="quiz-actions">
        <button id="recomecar-quiz" class="btn">Recomeçar Quiz</button>
        <a href="<?php echo BASE_URL?>/home" class="btn">Voltar ao Início</a>
    </div>
</div>


            <script>
                const perguntasQuiz = <?php echo json_encode($dados['perguntas'])?>;
            </script>
        </div>
    </section>
</main>

<script>
    
document.addEventListener('DOMContentLoaded', function() {
    let perguntaAtual = 0;
    let respostas = [];
    let perguntaIds = [];
    let acertos = 0;

    const perguntasQuiz = <?php echo json_encode($dados['perguntas'])?>;
    const totalPerguntas = <?php echo $dados['totalPerguntas']?>;

    // Elementos DOM
    const perguntaNumeroEl = document.getElementById('pergunta-numero');
    const perguntaTextoEl = document.getElementById('pergunta-texto');
    const alternativasContainer = document.getElementById('alternativas-container');
    const confirmarBtn = document.getElementById('confirmar-resposta');
    const perguntaArea = document.getElementById('pergunta-area');
    const feedbackArea = document.getElementById('feedback-area');
    const feedbackMsg = document.getElementById('feedback-mensagem');
    const proximaBtn = document.getElementById('proxima-pergunta');
    const resultadoFinal = document.getElementById('resultado-final');
    const resultadoTexto = document.getElementById('resultado-texto');

    // Mostrar a primeira pergunta
    mostrarPergunta(perguntaAtual);

    function mostrarPergunta(index) {
        if (index >= perguntasQuiz.length) {
            finalizarQuiz();
            return;
        }

        const pergunta = perguntasQuiz[index];
        perguntaTextoEl.textContent = pergunta.pergunta;
        perguntaNumeroEl.textContent = index + 1;

        // Embaralhar alternativas
        let alternativas = [
            pergunta.resposta_correta,
            pergunta.alternativa1,
            pergunta.alternativa2,
            pergunta.alternativa3
        ];
        alternativas = shuffleArray(alternativas);

        // Limpar alternativas anteriores
        alternativasContainer.innerHTML = '';

        // Adicionar novas alternativas
        alternativas.forEach(alt => {
            const label = document.createElement('label');
            label.className = 'alternativa';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'resposta';
            input.value = alt;

            input.addEventListener('change', function() {
                confirmarBtn.disabled = false;
            });

            label.appendChild(input);
            label.appendChild(document.createTextNode(alt));
            label.appendChild(document.createElement('br'));

            alternativasContainer.appendChild(label);
        });

        // Resetar botão de confirmação
        confirmarBtn.disabled = true;

        // Mostrar área de pergunta
        perguntaArea.style.display = 'block';
        feedbackArea.style.display = 'none';
    }

    // Evento para confirmar resposta
    confirmarBtn.addEventListener('click', function() {
        const respostaSelecionada = document.querySelector('input[name="resposta"]:checked');

        if (!respostaSelecionada) return;

        const pergunta = perguntasQuiz[perguntaAtual];
        respostas.push(respostaSelecionada.value);
        perguntaIds.push(pergunta.id);

        // Verificar se está correta
        const correta = respostaSelecionada.value === pergunta.resposta_correta;
        if (correta) acertos++;

        // Mostrar feedback
        perguntaArea.style.display = 'none';
        feedbackArea.style.display = 'block';

        if (correta) {
            feedbackMsg.innerHTML = '<span style="color: green;">✓ Resposta correta!</span>';
        } else {
            feedbackMsg.innerHTML = `
                <span style="color: red;">✗ Resposta incorreta!</span><br>
                A resposta correta é: <strong>${pergunta.resposta_correta}</strong>
            `;
        }
    });

    // Evento para próxima pergunta
    proximaBtn.addEventListener('click', function() {
        perguntaAtual++;
        if (perguntaAtual < perguntasQuiz.length) {
            mostrarPergunta(perguntaAtual);
        } else {
            finalizarQuiz();
        }
    });

    function finalizarQuiz() {
    fetch('<?php echo BASE_URL?>/finalizar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            respostas: respostas,
            perguntaIds: perguntaIds
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro na resposta do servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }

        // Mostrar resultado final
        document.getElementById('quiz-progress').style.display = 'none';
        perguntaArea.style.display = 'none';
        feedbackArea.style.display = 'none';
        resultadoFinal.style.display = 'block';

        resultadoTexto.textContent = `Você acertou ${data.acertos} de 5 perguntas (${data.porcentagem}%). Sua nova pontuação média é ${data.novaPontuacao}%.`;

        // Atualizar header com a nova pontuação
        const pontuacaoEl = document.querySelector('.user-score');
        if (pontuacaoEl) {
            pontuacaoEl.textContent = data.novaPontuacao + '%';
        }

        // Redirecionar após 5 segundos
        setTimeout(() => {
            window.location.href = data.redirect;
        }, 5000);
    })
    .catch(error => {
        console.error('Erro:', error);
        resultadoTexto.textContent = 'Erro ao processar o quiz: ' + error.message;
        resultadoFinal.style.display = 'block';
    });
}

    // Botão para recomeçar o quiz
    document.getElementById('recomecar-quiz').addEventListener('click', function() {
        location.reload(); // Recarrega a página para começar novo quiz
    });


    // Função para embaralhar array
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }
});
</script>