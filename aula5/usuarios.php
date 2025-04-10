<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin: 20px 0; padding: 15px; background: #f5f5f5; }
        input, button { padding: 8px; margin: 5px 0; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <h1>Sistema de Usuários</h1>
    
    <?php
    class Usuario {
        public $id;
        private $nome;
        private $sobrenome;
        private $idade;

        function __construct($id = null, $nome = "", $sobrenome = "", $idade = 0) {
            $this->id = $id;
            $this->nome = $nome;
            $this->sobrenome = $sobrenome;
            $this->idade = $idade;
        }

        // Métodos getters
        function getNome() {
            return $this->nome;
        }

        function getSobrenome() {
            return $this->sobrenome;
        }
        
        function getIdade() {
            return $this->idade;
        }
        
        // Métodos setters
        function setNome($nome) {
            $this->nome = $nome;
        }

        function setSobrenome($sobrenome) {
            $this->nome = $sobrenome;
        }
        
        function setIdade($idade) {
            $this->idade = $idade;
        }
    }

    // Conexão com o banco de dados
    $servername = 'localhost';
    $username = 'root';
    $password = 'aluno';
    $dbname = 'banco';

    $conn = new mysqli($servername, $username, $password, $dbname);
    
    if($conn->connect_error) {
        die("<div class='error'>Conexão falhou: " . $conn->connect_error . "</div>");
    }

    // Criar tabela se não existir
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        sobrenome VARCHAR(100) NOT NULL,
        idade INT NOT NULL
    )";
    
    if(!$conn->query($sql)) {
        echo "<div class='error'>Erro ao criar tabela: " . $conn->error . "</div>";
    }

    // Processar formulários
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['adicionar'])) {
            // Inserir novo usuário
            $nome = $conn->real_escape_string($_POST['nome']);
            $idade = intval($_POST['idade']);
            
            $sql = "INSERT INTO usuarios (nome, sobrenome, idade) VALUES ('$nome', '$sobrenome', $idade)";
            
            if($conn->query($sql)) {
                echo "<div class='message success'>Usuário adicionado com sucesso!</div>";
            } else {
                echo "<div class='message error'>Erro ao adicionar: " . $conn->error . "</div>";
            }
        }
        elseif(isset($_POST['envelhecer'])) {
            // Atualizar idade
            $id = intval($_POST['id']);
            $anos = intval($_POST['anos']);
            
            $sql = "UPDATE usuarios SET idade = idade + $anos WHERE id = $id";
            
            if($conn->query($sql)) {
                echo "<div class='message success'>Idade atualizada com sucesso!</div>";
            } else {
                echo "<div class='message error'>Erro ao atualizar: " . $conn->error . "</div>";
            }
        }
        elseif(isset($_POST['remover'])) {
            // Remover usuário
            $id = intval($_POST['id']);
            
            $sql = "DELETE FROM usuarios WHERE id = $id";
            
            if($conn->query($sql)) {
                echo "<div class='message success'>Usuário removido com sucesso!</div>";
            } else {
                echo "<div class='message error'>Erro ao remover: " . $conn->error . "</div>";
            }
        }
    }

    // Buscar usuários do banco
    $usuarios = array();
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id'], $row['nome'], $row['sobrenome'], $row['idade']);
        }
    }
    ?>

    <!-- Formulário para adicionar usuário -->
    <form method="post">
        <h2>Adicionar Usuário</h2>
        Nome: <input type="text" name="nome" required><br>
        Sobrenome: <input type="text" name="sobrenome" required><br>
        Idade: <input type="number" name="idade" required><br>
        <button type="submit" name="adicionar">Adicionar</button>
    </form>

    <!-- Formulário para envelhecer usuário -->
    <form method="post">
        <h2>Envelhecer Usuário</h2>
        ID do Usuário: <input type="number" name="id" required><br>
        Anos a adicionar: <input type="number" name="anos" min="1" required><br>
        <button type="submit" name="envelhecer">Envelhecer</button>
    </form>

    <!-- Formulário para remover usuário -->
    <form method="post">
        <h2>Remover Usuário</h2>
        ID do Usuário: <input type="number" name="id" required><br>
        <button type="submit" name="remover">Remover</button>
    </form>

    <!-- Tabela de usuários -->
    <h2>Usuários Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Sobrenome</th>
            <th>Idade</th>
        </tr>
        <?php 
        if(!empty($usuarios)) {
            foreach($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario->id ?></td>
                    <td><?= htmlspecialchars($usuario->getNome()) ?></td>
                    <td><?= htmlspecialchars($usuario->getSobrenome()) ?></td>
                    <td><?= $usuario->getIdade() ?></td>
                </tr>
            <?php endforeach; 
        } else {
            echo "<tr><td colspan='3'>Nenhum usuário cadastrado</td></tr>";
        }
        ?>
    </table>
    
    <?php
    // Fechar conexão
    $conn->close();
    ?>
</body>
</html>