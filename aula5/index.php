<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Usuários</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .user-info { margin: 10px 0; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <?php
    class Usuario {
        private $nome;
        private $idade;

        function __construct($nome = "", $idade = 0) {
            $this->nome = $nome;
            $this->idade = $idade;
        }

        function envelhecer($anos) {
            if ($anos > 0) {
                $this->idade += $anos;
            }
        } 

        // Métodos getters corretos
        function getNome() {
            return $this->nome;
        }
        
        function getIdade() {
            return $this->idade;
        }
        
        // Métodos setters para modificar as propriedades privadas
        function setNome($nome) {
            $this->nome = $nome;
        }
        
        function setIdade($idade) {
            $this->idade = $idade;
        }
    }

    // Criando usuários
    $usuario1 = new Usuario("Hudsaw", 46);

    $usuario2 = new Usuario();
    $usuario2->setNome("Giane"); 
    $usuario2->setIdade(49);    

    $usuarios = [$usuario1, $usuario2];
    
    // Exibir usuários (usando getters)
    echo "<h2>Usuários Iniciais</h2>";
    foreach($usuarios as $user) {
        echo "<div class='user-info'>";
        echo "Nome: " . $user->getNome() . ", Idade: " . $user->getIdade();
        echo "</div>";
    }

    // Envelhecer usuário
    $usuario1->envelhecer(1);
    
    // Exibir usuários após envelhecer
    echo "<h2>Usuários Após Envelhecer</h2>";
    foreach($usuarios as $user) {
        echo "<div class='user-info'>";
        echo "Nome: " . $user->getNome() . ", Idade: " . $user->getIdade();
        echo "</div>";
    }
    ?>
</body>
</html>