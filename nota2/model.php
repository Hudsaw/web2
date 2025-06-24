<?php
require_once 'constants.php';
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
        try {
            $stmt       = $this->pdo->query("SELECT id, nome FROM area_atuacao ORDER BY nome");
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $resultados;

        } catch (PDOException $e) {
            error_log("Erro grave ao buscar áreas: " . $e->getMessage());
            return [];
        }
    }

    public function countTodosCurriculos()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM cadastro");
        return $stmt->fetchColumn();
    }

    public function countCurriculosPorArea($areaId)
    {
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*)
        FROM cadastro
        WHERE area_atuacao_id = ?
    ");
        $stmt->execute([$areaId]);
        return $stmt->fetchColumn();
    }

    public function getTodosCurriculos($limit, $offset)
    {
        $stmt = $this->pdo->prepare("
        SELECT c.id, c.tipo, c.nome, c.email, c.telefone, a.nome AS area_nome
        FROM cadastro c
        LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id
        LIMIT :limit OFFSET :offset
    ");
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurriculosPorArea($areaId, $limit, $offset)
    {
        $stmt = $this->pdo->prepare("
        SELECT c.id, c.tipo, c.nome, c.email, c.telefone, a.nome AS area_nome
        FROM cadastro c
        JOIN area_atuacao a ON c.area_atuacao_id = a.id
        WHERE c.area_atuacao_id = :areaId
        LIMIT :limit OFFSET :offset
    ");
        $stmt->bindValue(':areaId', $areaId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($userData)
    {
        try {
            $senhaHash = password_hash($userData['senha'], PASSWORD_DEFAULT);

            $query = "INSERT INTO cadastro
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

            if (! $stmt->execute()) {
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
            $stmt = $this->pdo->prepare("SELECT id FROM cadastro WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Erro ao verificar email: " . $e->getMessage());
            return false;
        }
    }

    public function cpfExists($cpf)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM cadastro WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetch() !== false;
    }

    public function updateUser($userData)
    {
        try {
            $query = "UPDATE cadastro SET
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
                                    FROM cadastro c
                                    LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id
                                    WHERE c.id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por ID: " . $e->getMessage());
            return false;
        }
    }

    // Validação
    public function getCurriculoById($id)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT c.*, a.nome AS area_nome
                                    FROM cadastro c
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
            $stmt = $this->pdo->prepare("SELECT * FROM cadastro WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            return $usuario;
        } catch (PDOException $e) {
            error_log("Erro ao buscar usuário por email: " . $e->getMessage());
            return false;
        }
    }

    public function getPerguntasQuiz($areaId, $nivel)
    {
        error_log("Buscando perguntas para área $areaId e nível $nivel");
        try {
            $stmt = $this->pdo->prepare("
            SELECT p.*
            FROM perguntas p
            WHERE p.area_atuacao_id = :areaId
            AND p.nivel_id = (SELECT id FROM nivel WHERE nome = :nivel)
            ORDER BY RAND()
            LIMIT 10
        ");

            $nivelFormatado = ucfirst(strtolower($nivel));
            $stmt->bindParam(':areaId', $areaId, PDO::PARAM_INT);
            $stmt->bindParam(':nivel', $nivelFormatado);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar perguntas: " . $e->getMessage());
            return [];
        }
    }

    public function adicionarPergunta($dados)
    {
        error_log("Dados recebidos: " . print_r($dados, true));
        try {
            $query = "INSERT INTO perguntas
                 (pergunta, resposta_correta, alternativa1, alternativa2, alternativa3,
                  area_atuacao_id, nivel_id)
                 VALUES (:pergunta, :resposta_correta, :alternativa1, :alternativa2, :alternativa3,
                         :area_atuacao_id, :nivel_id)";

            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(':pergunta', $dados['pergunta']);
            $stmt->bindParam(':resposta_correta', $dados['resposta_correta']);
            $stmt->bindParam(':alternativa1', $dados['alternativa1']);
            $stmt->bindParam(':alternativa2', $dados['alternativa2']);
            $stmt->bindParam(':alternativa3', $dados['alternativa3']);
            $stmt->bindParam(':area_atuacao_id', $dados['area_atuacao_id'], PDO::PARAM_INT);
            $stmt->bindParam(':nivel_id', $dados['nivel_id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro PDO ao adicionar pergunta: " . $e->getMessage());
            return false;
        }
    }

    public function getPerguntasAleatorias($areaId)
    {
        error_log("Buscando perguntas aleatórias para área $areaId");
        try {
            $stmt = $this->pdo->prepare("
            (SELECT p.*, n.nome as nivel
             FROM perguntas p
             JOIN nivel n ON p.nivel_id = n.id
             WHERE p.area_atuacao_id = ? AND n.nome = 'Fácil'
             ORDER BY RAND() LIMIT 2)

             UNION

            (SELECT p.*, n.nome as nivel
             FROM perguntas p
             JOIN nivel n ON p.nivel_id = n.id
             WHERE p.area_atuacao_id = ? AND n.nome = 'Médio'
             ORDER BY RAND() LIMIT 2)

             UNION

            (SELECT p.*, n.nome as nivel
             FROM perguntas p
             JOIN nivel n ON p.nivel_id = n.id
             WHERE p.area_atuacao_id = ? AND n.nome = 'Difícil'
             ORDER BY RAND() LIMIT 1)
        ");
            $stmt->execute([$areaId, $areaId, $areaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar perguntas aleatórias: " . $e->getMessage());
            return [];
        }
    }

    public function atualizarPontuacao($userId, $avaliacao, $totalPerguntas) {
        error_log("Atualizando pontuação para usuário $userId: $avaliacao%, $totalPerguntas perguntas");
        try {
            $stmt = $this->pdo->prepare("
                UPDATE cadastro
                SET avaliacao = :avaliacao, 
                    total_perguntas = :total_perguntas,
                    atualizado_em = NOW()
                WHERE id = :id
            ");
            
            $stmt->bindParam(':avaliacao', $avaliacao, PDO::PARAM_INT);
            $stmt->bindParam(':total_perguntas', $totalPerguntas, PDO::PARAM_INT);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar pontuação: " . $e->getMessage());
            return false;
        }
    }

    public function verificarResposta($perguntaId, $resposta)
    {
        error_log("Verificando resposta para pergunta $perguntaId: $resposta");
        try {
            $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as correct
            FROM perguntas
            WHERE id = ? AND resposta_correta = ?
        ");
            $stmt->execute([$perguntaId, $resposta]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['correct'] > 0;
        } catch (PDOException $e) {
            error_log("Erro ao verificar resposta: " . $e->getMessage());
            return false;
        }
    }

    public function getRespostaCorreta($perguntaId)
    {
        error_log("Buscando resposta correta para pergunta $perguntaId");
        try {
            $stmt = $this->pdo->prepare("SELECT resposta_correta FROM perguntas WHERE id = ?");
            $stmt->execute([$perguntaId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['resposta_correta'] ?? '';
        } catch (PDOException $e) {
            error_log("Erro ao buscar resposta correta: " . $e->getMessage());
            return '';
        }
    }

    public function getPerguntasPaginadas($limit, $offset)
    {
        try {
            $stmt = $this->pdo->prepare("
            SELECT p.*, a.nome AS area_nome, n.nome AS nivel_nome
            FROM perguntas p
            JOIN area_atuacao a ON p.area_atuacao_id = a.id
            JOIN nivel n ON p.nivel_id = n.id
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ");
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar perguntas paginadas: " . $e->getMessage());
            return [];
        }
    }

    public function countTodasPerguntas()
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM perguntas");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erro ao contar perguntas: " . $e->getMessage());
            return 0;
        }
    }

    public function toggleStatusPergunta($perguntaId)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE perguntas SET ativa = NOT ativa WHERE id = ?");
            return $stmt->execute([$perguntaId]);
        } catch (PDOException $e) {
            error_log("Erro ao alternar status da pergunta: " . $e->getMessage());
            return false;
        }
    }

    public function excluirPergunta($perguntaId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM perguntas WHERE id = ?");
            return $stmt->execute([$perguntaId]);
        } catch (PDOException $e) {
            error_log("Erro ao excluir pergunta: " . $e->getMessage());
            return false;
        }
    }

    public function getNiveisDificuldade()
    {
        try {
            $stmt = $this->pdo->query("SELECT id, nome FROM nivel ORDER BY nome");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erro ao buscar níveis de dificuldade: " . $e->getMessage());
            return [];
        }
    }
}
