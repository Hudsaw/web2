<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/header.php';

// Verifica se o usuário está logado
$id_curriculo = 0;

if (isset($_GET['id'])) {
    $id_curriculo = (int)$_GET['id'];
} elseif (isset($_SESSION['id'])) {
    $id_curriculo = (int)$_SESSION['id'];
}

// Redireciona se nenhum ID válido foi encontrado
if ($id_curriculo <= 0) {
    header('Location: login.php');
    exit();
}

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare("SELECT * FROM curriculo WHERE id = ?");
    $stmt->execute([$id_curriculo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuário não encontrado");
    }
} catch (Exception $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
?>

    <div class="secao">
        <section id="pessoais">
            <h1><?php echo htmlspecialchars($usuario['nome'] ?? ''); ?></h1>
            <table>

                <tr>
                    <td><?php echo htmlspecialchars($usuario['resumo'] ?? ''); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>E-mail:<?php echo htmlspecialchars($usuario['email'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Telefone: <?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>CEP: <?php echo htmlspecialchars($usuario['cep'] ?? ''); ?></td>
                    <td></td>
                </tr>
            </table>
        </section>

        <section id="formacao">
            <br>
            <h2>Formação Acadêmica</h2>
            <table>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['escolaridade'] ?? ''); ?></td>
                </tr>
            </table>
        </section>

        <section id="experiencias">
            <br>
            <h2>Experiências Profissionais</h2>
            <table>
                <tr>
                    <td colspan="2"><?php echo nl2br(htmlspecialchars($usuario['experiencias'] ?? '')); ?></td>
                </tr>
            </table>
        </section>


    </div>
<?php require_once __DIR__ . '/rodape.php'; ?>

