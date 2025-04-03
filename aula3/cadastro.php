<?php
session_start();
$titulo = isset($_SESSION['titulo']) ? $_SESSION['titulo'] : "Título Padrão";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "Nome não informado";
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : "E-mail não informado";
    $frase = isset($_POST['frase']) ? htmlspecialchars($_POST['frase']) : "A frase não foi informada";

    echo "<h1>$titulo</h1>";
    echo "<p><strong>Nome:</strong> $name</p>";
    echo "<p><strong>E-mail:</strong> $email</p>";
    echo "<p><strong>Frase:</strong> $frase</p>";
    echo "<p><strong>Palindromo:</strong> ". strrev($frase) ."</p>";
} else {
    echo "<h1>Erro</h1>";
    echo "<p>O formulário não foi enviado corretamente.</p>";
}
?>