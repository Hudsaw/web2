<?php
session_start();
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
    
    <a href="index.php">Voltar</a>


</body>

</html>