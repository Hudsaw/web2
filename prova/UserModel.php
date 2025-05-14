<?php
require_once __DIR__ . '/database.php';

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM curriculo WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($userData) {
    try {
        $senhaHash = password_hash($userData['senha'], PASSWORD_DEFAULT);
        
        $query = "INSERT INTO curriculo 
                 (nome, email, cpf, telefone, cep, complemento, linkedin, github, 
                 escolaridade, resumo, experiencias, senha) 
                 VALUES (:nome, :email, :cpf, :telefone, :cep, :complemento, :linkedin, :github, 
                 :escolaridade, :resumo, :experiencias, :senha)";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':nome', $userData['nome']);
        $stmt->bindParam(':telefone', $userData['telefone']);
        $stmt->bindParam(':cpf', $userData['cpf']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':cep', $userData['cep']);
        $stmt->bindParam(':complemento', $userData['complemento']);
        $stmt->bindParam(':linkedin', $userData['linkedin']);
        $stmt->bindParam(':github', $userData['github']);
        $stmt->bindParam(':escolaridade', $userData['escolaridade']);
        $stmt->bindParam(':resumo', $userData['resumo']);
        $stmt->bindParam(':experiencias', $userData['experiencias']);
        $stmt->bindParam(':senha', $senhaHash);
        
        if (!$stmt->execute()) {
            error_log("Erro na execução: " . print_r($stmt->errorInfo(), true));
            return false;
        }
        
        return $this->pdo->lastInsertId();
        
    } catch (PDOException $e) {
        error_log("Erro PDO ao criar usuário: " . $e->getMessage());
        return false;
    }
}

    public function cpfExists($cpf) {
        $stmt = $this->pdo->prepare("SELECT id FROM curriculo WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }
    
}