<?php
session_start();
$_SESSION['titulo'] = "Página PHP";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['titulo']; ?></title>
</head>
<body>
    <form action="cadastro.php" method="POST">
        Name: <input type="text" name="name"><br>
        E-mail: <input type="text" name="email" required><br>
        Frase: <input type="text" name="frase"><br>
        <input type="submit">
    </form>
</body>
</html>