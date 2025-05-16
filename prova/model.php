<?php
require_once 'config.php';
require_once 'database.php';

class Model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    // Métodos do usuário
    public function getAreasAtuacao()
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM area_atuacao ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurriculosPorArea($areaId, $limit, $offset)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, telefone FROM curriculo 
                                    WHERE area_atuacao_id = ? LIMIT ? OFFSET ?");
        $stmt->execute([$areaId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countCurriculosPorArea($areaId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM curriculo WHERE area_atuacao_id = ?");
        $stmt->execute([$areaId]);
        return $stmt->fetchColumn();
    }

    public function createUser($userData)
    {
        try {
            $senhaHash = password_hash($userData['senha'], PASSWORD_DEFAULT);

            $query = "INSERT INTO curriculo 
             (nome, email, cpf, telefone, cep, complemento, linkedin, github, 
             escolaridade, resumo, experiencias, senha, area_atuacao_id) 
             VALUES (:nome, :email, :cpf, :telefone, :cep, :complemento, :linkedin, :github, 
             :escolaridade, :resumo, :experiencias, :senha, :area_atuacao_id)";

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
            $stmt->bindParam(':area_atuacao_id', $userData['area_atuacao_id']);

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

    public function emailExists($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM curriculo WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    public function cpfExists($cpf)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM curriculo WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }

    public function updateUser($userData)
    {
        try {
            $query = "UPDATE curriculo SET 
                 nome = :nome, 
                 telefone = :telefone, 
                 cep = :cep, 
                 complemento = :complemento, 
                 linkedin = :linkedin, 
                 github = :github, 
                 escolaridade = :escolaridade, 
                 resumo = :resumo, 
                 experiencias = :experiencias
                 WHERE id = :id";

            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':id', $userData['id']);
            $stmt->bindParam(':nome', $userData['nome']);
            $stmt->bindParam(':telefone', $userData['telefone']);
            $stmt->bindParam(':cep', $userData['cep']);
            $stmt->bindParam(':complemento', $userData['complemento']);
            $stmt->bindParam(':linkedin', $userData['linkedin']);
            $stmt->bindParam(':github', $userData['github']);
            $stmt->bindParam(':escolaridade', $userData['escolaridade']);
            $stmt->bindParam(':resumo', $userData['resumo']);
            $stmt->bindParam(':experiencias', $userData['experiencias']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro PDO ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT c.*, a.nome AS area_nome 
                                    FROM curriculo c 
                                    LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id 
                                    WHERE c.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return false;
        }
    }

    public function getCurriculoById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT c.*, a.nome AS area_nome 
                                    FROM curriculo c 
                                    LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id 
                                    WHERE c.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar currículo por ID: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM curriculo WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return false;
        }
    }
}
