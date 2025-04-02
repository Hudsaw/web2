<?php
session_start();
$titulo = isset($_SESSION['titulo']) ? $_SESSION['titulo'] : "Título Padrão";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "Nome não informado";
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "E-mail não informado";

    echo "<h1>$titulo</h1>";
    echo "<p><strong>Nome:</strong> $name</p>";
    echo "<p><strong>E-mail:</strong> $email</p>";
} else {
    echo "<h1>Erro</h1>";
    echo "<p>O formulário não foi enviado corretamente.</p>";
}
?>