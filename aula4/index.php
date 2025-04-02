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
    <?php
        $a = 25;
        echo "<p>A variável 'a' tem o valor: $a </p>";

        $lista = array("IFSC", 55, "UFSC", "IFC");
        echo "Terceiro: $lista[2] <br>";

        for ($i = 0; $i < sizeof($lista); $i++) {
            echo "<p>Valor da posição " . ($i + 1) . " na lista é: $lista[$i]</p>";
        }

        $matriz = [["nome" => "Hudsaw", "idade" => 46], ["nome" => "Giane", "idade" => 49]];
        foreach ($matriz as $linha) {
            echo "<pre>";
            foreach ($linha as $chave => $valor) {
                echo ucfirst($chave). ": ". $valor . " \t ";
            }
            echo "\n";
            echo "</pre>";
        }
    ?>
</body>
</html>