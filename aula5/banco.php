<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Usuários</title>
    <style>
        /* Cores base */
        :root {
            --dark-blue: #2d2d3a;
            --darker-blue: #20202a;
            --yellow: #FFC107;
            --white: #FFFFFF;
            --black: #000000;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--darker-blue);
            color: var(--white);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: var(--dark-blue);
            border-radius: 8px;
        }
        
        h1, h2 {
            color: var(--yellow);
            margin-bottom: 20px;
        }
        
        /* Layout dos formulários */
        .form-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .form-box {
            flex: 1;
            min-width: 300px;
            background-color: var(--darker-blue);
            padding: 20px;
            border-radius: 8px;
        }
        
        /* Estilos da tabela */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            color: var(--white);
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--yellow);
        }
        
        th {
            background-color: var(--darker-blue);
            color: var(--yellow);
        }
        
        tr:hover {
            background-color: rgba(255, 193, 7, 0.1);
        }
        
        /* Estilos dos formulários */
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid var(--yellow);
            border-radius: 4px;
            background-color: var(--dark-blue);
            color: var(--white);
        }
        
        button {
            background-color: var(--yellow);
            color: var(--black);
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        
        button:hover {
            opacity: 0.9;
        }
        
        /* Mensagens */
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }
        
        .error {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .form-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-box {
                min-width: auto;
            }
            
            .container {
                padding: 15px;
            }
            
            th, td {
                padding: 8px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
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

            function getNome() { return $this->nome; }
            function getSobrenome() { return $this->sobrenome; }
            function getIdade() { return $this->idade; }
            function setNome($nome) { $this->nome = $nome; }
            function setSobrenome($sobrenome) { $this->sobrenome = $sobrenome; }
            function setIdade($idade) { $this->idade = $idade; }
        }

        // Conexão com o banco de dados
        $servername = 'localhost';
        $username = 'root';
        $password = '';
        $dbname = 'banco';

        $conn = new mysqli($servername, $username, $password, $dbname);
        
        if($conn->connect_error) {
            die("<div class='message error'>Conexão falhou: " . $conn->connect_error . "</div>");
        }

        // Criar tabela se não existir
        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            sobrenome VARCHAR(100) NOT NULL,
            idade INT NOT NULL
        )";
        
        if(!$conn->query($sql)) {
            echo "<div class='message error'>Erro ao criar tabela: " . $conn->error . "</div>";
        }

        // Processar mensagens
        if(isset($_GET['success'])) {
            $message = "";
            switch($_GET['success']) {
                case 'add': $message = "Usuário adicionado com sucesso!"; break;
                case 'update': $message = "Idade atualizada com sucesso!"; break;
                case 'delete': $message = "Usuário removido com sucesso!"; break;
            }
            echo "<div class='message success'>$message</div>";
        }

        // Processar formulários
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            if(isset($_POST['adicionar'])) {
                $nome = $conn->real_escape_string($_POST['nome']);
                $sobrenome = $conn->real_escape_string($_POST['sobrenome']);
                $idade = intval($_POST['idade']);
                
                $sql = "INSERT INTO usuarios (nome, sobrenome, idade) VALUES ('$nome', '$sobrenome', $idade)";
                
                if($conn->query($sql)) {
                    header("Location: ".$_SERVER['PHP_SELF']."?success=add");
                    exit();
                } else {
                    echo "<div class='message error'>Erro ao adicionar: " . $conn->error . "</div>";
                }
            }
            elseif(isset($_POST['envelhecer'])) {
                $id = intval($_POST['id']);
                $anos = intval($_POST['anos']);
                
                $sql = "UPDATE usuarios SET idade = idade + $anos WHERE id = $id";
                
                if($conn->query($sql)) {
                    header("Location: ".$_SERVER['PHP_SELF']."?success=update");
                    exit();
                } else {
                    echo "<div class='message error'>Erro ao atualizar: " . $conn->error . "</div>";
                }
            }
            elseif(isset($_POST['remover'])) {
                $id = intval($_POST['id']);
                
                $sql = "DELETE FROM usuarios WHERE id = $id";
                
                if($conn->query($sql)) {
                    header("Location: ".$_SERVER['PHP_SELF']."?success=delete");
                    exit();
                } else {
                    echo "<div class='message error'>Erro ao remover: " . $conn->error . "</div>";
                }
            }
        }

        // Buscar usuários
        $usuarios = array();
        $sql = "SELECT * FROM usuarios";
        $result = $conn->query($sql);
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $usuarios[] = new Usuario($row['id'], $row['nome'], $row['sobrenome'], $row['idade']);
            }
        }
        ?>

<div class="form-container">
            <!-- Formulário de Adicionar -->
            <div class="form-box">
                <form method="post">
                    <h2>Adicionar Usuário</h2>
                    <label>Nome:</label>
                    <input type="text" name="nome" required>
                    
                    <label>Sobrenome:</label>
                    <input type="text" name="sobrenome" required>
                    
                    <label>Idade:</label>
                    <input type="number" name="idade" required>
                    
                    <button type="submit" name="adicionar">Adicionar</button>
                </form>
            </div>
            
            <!-- Formulário de Envelhecer -->
            <div class="form-box">
                <form method="post">
                    <h2>Envelhecer Usuário</h2>
                    <label>ID do Usuário:</label>
                    <input type="number" name="id" required>
                    
                    <label>Anos a adicionar:</label>
                    <input type="number" name="anos" min="1" required>
                    
                    <button type="submit" name="envelhecer">Envelhecer</button>
                </form>
            </div>
            
            <!-- Formulário de Remover -->
            <div class="form-box">
                <form method="post">
                    <h2>Remover Usuário</h2>
                    <label>ID do Usuário:</label>
                    <input type="number" name="id" required>
                    
                    <button type="submit" name="remover">Remover</button>
                </form>
            </div>
        </div>

        <h2>Usuários Cadastrados</h2>
        <?php if(!empty($usuarios)): ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Sobrenome</th>
                    <th>Idade</th>
                </tr>
                <?php foreach($usuarios as $usuario): ?>
                    <tr>
                        <td><?= $usuario->id ?></td>
                        <td><?= htmlspecialchars($usuario->getNome()) ?></td>
                        <td><?= htmlspecialchars($usuario->getSobrenome()) ?></td>
                        <td><?= $usuario->getIdade() ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>Nenhum usuário cadastrado.</p>
        <?php endif; ?>
    </div>
    
    <?php $conn->close(); ?>
</body>
</html>