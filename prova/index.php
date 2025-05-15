<?php
require_once __DIR__ . '/database.php';

// Inicia sessão de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,
        'cookie_httponly' => true
    ]);
}

$erro = null;

// Processa o formulário de login (mantido do código original)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // ... (mantenha o código de login existente)
}

// Configurações de paginação
$itensPorPagina = 10;
$paginaAtual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$escolaridadeFiltro = isset($_GET['escolaridade']) ? $_GET['escolaridade'] : '';

// Inclui o header.php
require_once __DIR__ . '/header.php';
?>

<main class="apresentacao">
    <section class="hero">
        <h1>Bem-vindo à Curriculum Premium</h1>
        <p>Conectamos talentos promissores às maiores empresas do país.</p>
        <p>Compramos currículos qualificados e os revendemos com exclusividade para recrutadores de alto nível.</p>
        <a href="<?php echo BASE_URL; ?>cadastro.php" class="btn">Cadastre seu currículo</a>
    </section>

    <!-- Seção de Busca por Escolaridade -->
    <section class="busca-curriculos">
        <h2>Buscar Currículos por Escolaridade</h2>

        <form method="get" class="form-busca">
            <div class="form-group">
                <label for="escolaridade">Escolaridade:</label>
                <select id="escolaridade" name="escolaridade" required>
                    <option value="">Selecione...</option>
                    <option value="fundamental" <?= $escolaridadeFiltro === 'fundamental' ? 'selected' : '' ?>>Fundamental Completo</option>
                    <option value="medio" <?= $escolaridadeFiltro === 'medio' ? 'selected' : '' ?>>Médio Completo</option>
                    <option value="superior" <?= $escolaridadeFiltro === 'superior' ? 'selected' : '' ?>>Superior Completo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-buscar">Buscar</button>
        </form>

        <?php
        // Se houver filtro, mostra os resultados
        if (!empty($escolaridadeFiltro)) {
            try {
                $pdo = Database::getInstance();

                // Conta o total de registros
                $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM curriculo WHERE escolaridade = ?");
                $stmtCount->execute([$escolaridadeFiltro]);
                $totalRegistros = $stmtCount->fetchColumn();

                // Calcula a paginação
                $totalPaginas = ceil($totalRegistros / $itensPorPagina);
                $offset = ($paginaAtual - 1) * $itensPorPagina;

                // Busca os registros paginados
                $stmt = $pdo->prepare("SELECT id, nome, email, telefone, escolaridade FROM curriculo 
                                      WHERE escolaridade = ? 
                                      LIMIT ? OFFSET ?");
                $stmt->bindValue(1, $escolaridadeFiltro);
                $stmt->bindValue(2, $itensPorPagina, PDO::PARAM_INT);
                $stmt->bindValue(3, $offset, PDO::PARAM_INT);
                $stmt->execute();
                $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($resultados) > 0) {
                    echo '<div class="resultados-busca">';
                    echo '<h3>Resultados da Busca</h3>';
                    echo '<p>Mostrando ' . count($resultados) . ' de ' . $totalRegistros . ' currículos encontrados</p>';

                    echo '<table class="tabela-curriculos">';
                    echo '<thead><tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Escolaridade</th>
                            <th>Visitar</th>
                          </tr></thead>';
                    echo '<tbody>';

                    foreach ($resultados as $curriculo) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($curriculo['nome']) . '</td>';
                        echo '<td>' . htmlspecialchars($curriculo['email']) . '</td>';
                        echo '<td>';
                        switch ($curriculo['escolaridade']) {
                            case 'fundamental':
                                echo 'Fundamental';
                                break;
                            case 'medio':
                                echo 'Médio';
                                break;
                            case 'superior':
                                echo 'Superior';
                                break;
                            default:
                                echo $curriculo['escolaridade'];
                        }
                        echo '</td>';
                        echo '<td><a href="' . BASE_URL . 'curriculo.php?id=' . $curriculo['id'] . '" class="btn-ver">Ver Currículo</a></td>';
                        echo '</tr>';
                    }

                    echo '</tbody></table>';

                    // Exibe a paginação
                    if ($totalPaginas > 1) {
                        echo '<div class="paginacao">';
                        if ($paginaAtual > 1) {
                            echo '<a href="?escolaridade=' . $escolaridadeFiltro . '&pagina=' . ($paginaAtual - 1) . '">&laquo; Anterior</a> ';
                        }

                        for ($i = 1; $i <= $totalPaginas; $i++) {
                            if ($i == $paginaAtual) {
                                echo '<span class="pagina-atual">' . $i . '</span> ';
                            } else {
                                echo '<a href="?escolaridade=' . $escolaridadeFiltro . '&pagina=' . $i . '">' . $i . '</a> ';
                            }
                        }

                        if ($paginaAtual < $totalPaginas) {
                            echo '<a href="?escolaridade=' . $escolaridadeFiltro . '&pagina=' . ($paginaAtual + 1) . '">Próxima &raquo;</a>';
                        }
                        echo '</div>';
                    }

                    echo '</div>';
                } else {
                    echo '<p class="sem-resultados">Nenhum currículo encontrado com esta escolaridade.</p>';
                }
            } catch (PDOException $e) {
                echo '<p class="erro-busca">Erro ao buscar currículos: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
        }
        ?>
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

<?php require_once __DIR__ . '/rodape.php'; ?>