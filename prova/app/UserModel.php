<?php
class UserModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function authenticate($email, $password)
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['senha'])) {
            unset($user['senha']);
            return $user;
        }

        return false;
    }

    public function createUser(array $userData)
    {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios
                (nome, email, senha, tipo, area_atuacao_id)
                VALUES (?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $userData['nome'],
                $userData['email'],
                password_hash($userData['senha'], PASSWORD_DEFAULT),
                $userData['tipo'] ?? 'candidato',
                $userData['area_atuacao_id'] ?? null,
            ]);

            return $success ? $this->pdo->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($userId, $data)
    {
        try {
            // Prepare os dados para atualização
            $updateData = [
                'nome'            => $data['nome'],
                'telefone'        => $data['telefone'],
                'cpf'             => $data['cpf'],
                'cep'             => $data['cep'],
                'complemento'     => $data['complemento'],
                'area_atuacao_id' => $data['area_atuacao_id'],
                'escolaridade'    => $data['escolaridade'],
                'resumo'          => $data['resumo'],
                'experiencias'    => $data['experiencias'],
                'linkedin'        => $data['linkedin'] ?? null,
                'github'          => $data['github'] ?? null,
            ];

            // Se a senha foi fornecida, atualize-a também
            if (! empty($data['senha'])) {
                $updateData['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
            }

            // Execute a atualização no banco de dados
            $stmt = $this->db->prepare("UPDATE usuarios SET ... WHERE id = :id");
            $stmt->bindValue(':id', $userId);
            // bind outros valores...

            return $stmt->execute();
        } catch (PDOException $e) {
            // Log do erro
            return false;
        }
    }

    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, a.nome as area_nome
            FROM usuarios u
            LEFT JOIN area_atuacao a ON u.area_atuacao_id = a.id
            WHERE u.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    public function getAreasAtuacao()
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM area_atuacao ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
