<?php

define('BASE_URL', 'http://localhost/web2/prova/');

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
 
     $this->pdo = new PDO(
                'mysql:host=localhost;dbname=curriculo', 
                'root', 
                ''
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}