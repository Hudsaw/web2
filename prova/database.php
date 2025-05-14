<?php

 class Database {
     private static $instance = null;
 
     // Configurações de conexão com o banco de dados
     private static $host = 'localhost'; 
     private static $dbname = 'curriculo'; 
     private static $username = 'root'; 
     private static $password = ''; 
 
     // Método para obter uma instância única da conexão (Singleton Pattern)
     public static function getInstance() {
         if (self::$instance === null) {
             try {
                 // Configura opções de conexão
                 $options = [
                     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                     PDO::ATTR_EMULATE_PREPARES => false, 
                 ];
 
                 // Cria a conexão PDO
                 self::$instance = new PDO(
                     "mysql:host=" . self::$host . ";dbname=" . self::$dbname . ";charset=utf8",
                     self::$username,
                     self::$password,
                     $options
                 );
             } catch (PDOException $e) {
                 // Em caso de erro, exibe mensagem e encerra o script
                 die("Erro ao conectar ao banco de dados: " . $e->getMessage());
             }
         }
 
         return self::$instance;
     }
 }