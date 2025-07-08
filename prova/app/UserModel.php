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

    public function createUser(array $data)
{
    try {
        $stmt = $this->pdo->prepare("
            INSERT INTO usuarios
            (nome, email, senha, tipo, avaliacao, total_perguntas, area_atuacao_id, cpf, telefone, cep, complemento, escolaridade, resumo, experiencias, linkedin, github)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $success = $stmt->execute([
            $data['nome'],
            $data['email'],
            password_hash($data['senha'], PASSWORD_DEFAULT),
            'candidato',
            0, // avaliacao
            0, // total_perguntas
            $data['area_atuacao_id'] ?? null,
            $data['cpf'],
            $data['telefone'],
            $data['cep'],
            $data['complemento'],
            $data['escolaridade'],
            $data['resumo'],
            $data['experiencias'],
            $data['linkedin'] ?? null,
            $data['github'] ?? null,
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
        $campos = [
            'nome'      => $data['nome'],
            'email'     => $data['email'],
            'telefone'  => $data['telefone'],
            'cpf'       => $data['cpf'],
            'cep'       => $data['cep'],
            'complemento' => $data['complemento'],
            'area_atuacao_id' => $data['area_atuacao_id'],
            'escolaridade' => $data['escolaridade'],
            'resumo'     => $data['resumo'],
            'experiencias' => $data['experiencias'],
            'linkedin'  => $data['linkedin'] ?? null,
            'github'    => $data['github'] ?? null,
        ];

        // Adicionar senha se fornecida
        if (!empty($data['senha'])) {
            $campos['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        }

        $setParts = [];
        $params = [];
        
        foreach ($campos as $campo => $value) {
            $setParts[] = "{$campo} = ?";
            $params[] = $value;
        }
        
        $params[] = $userId;
        
        $query = "UPDATE usuarios SET " . implode(', ', $setParts) . " WHERE id = ?";
        
        // Executar a atualização
        $stmt = $this->pdo->prepare($query);
        $success = $stmt->execute($params);

        return $success;
    } catch (PDOException $e) {
        error_log("Error updating user: " . $e->getMessage());
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

    public function cpfExists($cpf)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }

    public function getAreasAtuacao()
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM area_atuacao ORDER BY nome");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserScore($userId)
{
    $stmt = $this->pdo->prepare("
        SELECT avaliacao as correct, total_perguntas as total 
        FROM usuarios
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['correct' => 0, 'total' => 0];
}
}
