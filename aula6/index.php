<?php
session_start();
if(isset($_POST['usuario'], $_POST['senha'])){
    if($_POST['usuario']== 'maria' && $_POST['senha']=='123'){
        $_SESSION['usuario'] = $_POST['usuario'];
        header ('location:continua.php');
    }
}
if(isset($_GET['erro'])){
    $erro = 'Logue primeiro arrombado';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style.css">
    <title>Document</title>
</head>

<body>
    <?php
    if (isset($_COOKIE['nome'])) {
        echo "Valor em nome: " . $_COOKIE['nome'];
    } else {
        echo "Definido o cookie do 'nome'!<br>";
        setcookie('nome', 'Hudsaw', time() + 3600);
    }
    ?>
    <form action="" method="post">
        <input type= "text" name="usuario" placeholder="Usuário">
        <input type= "text" name="senha" placeholder="Senha"> 
        <input type= "submit" name="login" placeholder="Login"> 
    </form>

    <?php
    $_SESSION["favcolor"] = "green";
    $_SESSION["favanimal"] = "cat";
    echo "<br>Sessão setada<br>";
    ?>

<a href="continua.php">Continuar</a>

</body>

</html>