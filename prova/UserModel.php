<?php
namespace App\Models;

use PDO; 
use PDOException;

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function emailExists($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
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
        
        $query = "INSERT INTO usuarios 
                 (nome, email, senha, tipo, cpf, telefone, cep, complemento, 
                  especialidade_id, plano_id, crm, ativo, data_cadastro) 
                 VALUES (:nome, :email, :senha, :tipo, :cpf, :telefone, :cep, :complemento, 
                         :especialidade_id, :plano_id, :crm, 0, NOW())";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->bindParam(':nome', $userData['nome']);
        $stmt->bindParam(':telefone', $userData['telefone']);
        $stmt->bindParam(':cpf', $userData['cpf']);
        $stmt->bindParam(':email', $userData['email']);
        $stmt->bindParam(':cep', $userData['cep']);
        $stmt->bindParam(':complemento', $userData['complemento']);
        $stmt->bindParam(':tipo', $userData['tipo']);
        $stmt->bindParam(':especialidade_id', $userData['especialidade_id'], PDO::PARAM_INT);
        $stmt->bindParam(':crm', $userData['crm']);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':plano_id', $userData['plano_id'], PDO::PARAM_INT);
        
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

    public function getCartoesSalvos($usuarioId) {
        $stmt = $this->pdo->prepare("SELECT * FROM cartoes_credito WHERE usuario_id = ?");
        $stmt->execute([$usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, tipo, especialidade_id FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cpfExists($cpf) {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, tipo_usuario FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createPasswordResetToken($userId, $token) {
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $this->pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $token, $expiry]);
    }

    /**
     * Conta o total de usuários ativos
     */
    public function getTotalUsers($tipo = null) {
        $where = $tipo ? "WHERE tipo = :tipo" : "";
        $sql = "SELECT COUNT(*) as total FROM usuarios $where";
        $stmt = $this->pdo->prepare($sql);
        if ($tipo) {
            $stmt->bindValue(':tipo', $tipo);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Conta usuários por tipo
     */
    public function getUsersByType(string $type): int {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE tipo = ? AND ativo = 1");
            $stmt->execute([$type]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar usuários por tipo: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllUsers($tipo = null) {
        try {
            $sql = "SELECT id, nome, email, tipo, ativo, data_cadastro FROM usuarios";
            
            if ($tipo) {
                $sql .= " WHERE tipo = :tipo";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->query($sql);
            }
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Garante que sempre retorne array
            return is_array($result) ? $result : [];
            
        } catch (PDOException $e) {
            error_log("Erro no getAllUsers: " . $e->getMessage());
            return [];
        }
    }
    public function deactivateUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ativo = 0 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Erro ao desativar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    public function activateUser($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE usuarios SET ativo = 1 WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Erro ao ativar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateUser($userId, $data) {
        try {
            $query = "UPDATE usuarios SET 
                     nome = :nome, 
                     email = :email, 
                     telefone = :telefone,
                     tipo = :tipo,
                     especialidade_id = :especialidade_id,
                     plano_id = :plano_id
                     WHERE id = :id";
            
            $stmt = $this->pdo->prepare($query);
            
            $stmt->bindParam(':nome', $data['nome']);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':telefone', $data['telefone']);
            $stmt->bindParam(':tipo', $data['tipo']);
            $stmt->bindParam(':especialidade_id', $data['especialidade_id'], PDO::PARAM_INT);
            $stmt->bindParam(':plano_id', $data['plano_id'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllMedicos(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT id, nome 
                FROM usuarios 
                WHERE tipo = 'medico' 
                AND ativo = 1
                ORDER BY nome
            ");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                error_log("AVISO: Nenhum médico ativo encontrado na tabela usuarios");
            }

            return $result;
        } catch (PDOException $e) {
            error_log("ERRO ao buscar médicos: " . $e->getMessage());
            return [];
        }
    }

    public function getAllEspecialistas(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT u.id, u.nome, te.nome as especialidade 
                FROM usuarios u
                LEFT JOIN tipos_exame te ON te.id = u.especialidade_id 
                WHERE u.tipo = 'especialista' 
                AND u.ativo = 1
                ORDER BY u.nome
            ");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                error_log("AVISO: Nenhum especialista ativo encontrado na tabela usuarios");
                return [];
            }

            // Garantir que todos os elementos tenham a chave 'especialidade'
            return array_map(function($especialista) {
                return [
                    'id' => $especialista['id'],
                    'nome' => $especialista['nome'],
                    'especialidade' => $especialista['especialidade'] ?? 'N/A'
                ];
            }, $result);

        } catch (PDOException $e) {
            error_log("ERRO ao buscar especialistas: " . $e->getMessage());
            return [];
        }
    }
    
}

?>