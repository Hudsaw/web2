<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header ('location:index.php');
}
echo "<body style='background-color: red'>
    <h1>Maria</h1>
    </body>"
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
    echo "Cor favorita: " . $_SESSION["favcolor"] . ".<br>";
    echo "Cor favorita: " . $_SESSION["favanimal"] . ".<br>";
    ?>
    
    <a href="finaliza.php">Finaliza</a>

    <?php
    session_destroy();
    ?>


</body>

</html>