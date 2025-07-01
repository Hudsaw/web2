<main class="container">
    <section class="card">
        <h1><?= $dados['titulo'] ?></h1>
        
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-success"><?= $_SESSION['mensagem'] ?></div>
            <?php unset($_SESSION['mensagem']); ?>
        <?php endif; ?>
        
        <div class="admin-actions">
            <a href="<?= BASE_URL ?>/adicionarPergunta" class="btn">Adicionar Nova Pergunta</a>
            <span class="pagination-info">
                Mostrando <?= count($dados['perguntas']) ?> de <?= $dados['totalPerguntas'] ?> perguntas
            </span>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pergunta</th>
                    <th>Área</th>
                    <th>Nível</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dados['perguntas'] as $pergunta): ?>
                    <tr>
                        <td><?= $pergunta['id'] ?></td>
                        <td><?= htmlspecialchars($pergunta['pergunta']) ?></td>
                        <td><?= htmlspecialchars($pergunta['area_nome']) ?></td>
                        <td><?= htmlspecialchars($pergunta['nivel_nome']) ?></td>
                        <td class="actions">
                            <?php if ($pergunta['ativa']): ?>
                                <a href="<?= BASE_URL ?>/toggleStatus&id=<?= $pergunta['id'] ?>" class="btn btn-sm btn-disable">Desativar</a>
                            <?php else: ?>
                                <a href="<?= BASE_URL ?>/toggleStatus&id=<?= $pergunta['id'] ?>" class="btn btn-sm btn-enable">Ativar</a>
                            <?php endif; ?>
                            <a href="<?= BASE_URL ?>/excluirPergunta&id=<?= $pergunta['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta pergunta?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Paginação -->
        <?php if ($dados['totalPaginas'] > 1): ?>
            <div class="pagination">
                <?php if ($dados['paginaAtual'] > 1): ?>
                    <a href="<?= BASE_URL ?>/admin&pagina=<?= $dados['paginaAtual'] - 1 ?>" class="btn">&laquo; Anterior</a>
                <?php endif; ?>
                
                <span class="page-info">Página <?= $dados['paginaAtual'] ?> de <?= $dados['totalPaginas'] ?></span>
                
                <?php if ($dados['paginaAtual'] < $dados['totalPaginas']): ?>
                    <a href="<?= BASE_URL ?>/admin&pagina=<?= $dados['paginaAtual'] + 1 ?>" class="btn">Próxima &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>
</main>