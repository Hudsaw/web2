<div class="container">
    <div class="card">
        <h1>Resultados da Busca</h1>

        <?php if (!empty($areaFiltro)): ?>
            <p>Filtrando por: <?= htmlspecialchars($areas[$areaFiltro-1]['nome'] ?? 'Área desconhecida') ?></p>
        <?php endif; ?>
        

        <div class="resultados">
            <?php if (!empty($resultados)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Área</th>
                                <th>Contato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados as $curriculo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($curriculo['nome']) ?></td>
                                    <td><?= htmlspecialchars($curriculo['area_nome'] ?? 'Não informado') ?></td>
                                    <td><?= htmlspecialchars($curriculo['email']) ?></td>
                                    <td>
                                        <a href="<?= BASE_URL ?>?action=curriculo&id=<?= $curriculo['id'] ?>" class="btn-ver">
                                            Ver Currículo
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Paginação -->
                    <?php if ($totalPaginas > 1): ?>
                        <div class="paginacao">
                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <div class="pagina">
                                    <a  href="<?= BASE_URL ?>?action=busca&area=<?= $areaFiltro ?>&pagina=<?= $i ?>"
                                    class="<?= $i == $paginaAtual ? 'ativo' : 'inativo' ?>">
                                    <?= $i ?>
                                    </a>
                                </div> 
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="sem-resultados">Nenhum currículo encontrado.</p>
                <?php endif; ?>
                </div>
        </div>
    </div>
</div>