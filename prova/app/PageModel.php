<?php
class PageModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCurriculos($areaId = null, $limit = 10, $offset = 0)
    {
        $where = $areaId ? "WHERE c.area_atuacao_id = :areaId" : "";

        $stmt = $this->pdo->prepare("
            SELECT c.*, a.nome AS area_nome
            FROM usuarios c
            LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id
            $where
            LIMIT :limit OFFSET :offset
        ");

        if ($areaId) {
            $stmt->bindValue(':areaId', $areaId, PDO::PARAM_INT);
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countCurriculos($areaId = null)
    {
        $where = $areaId ? "WHERE area_atuacao_id = ?" : "";
        $stmt  = $this->pdo->prepare("SELECT COUNT(*) FROM usuarios $where");

        if ($areaId) {
            $stmt->execute([$areaId]);
        } else {
            $stmt->execute();
        }

        return $stmt->fetchColumn();
    }

    public function getCurriculoPorId(int $curriculoId): ?array
    {
        $stmt = $this->pdo->prepare("
        SELECT c.*, a.nome AS area_nome
        FROM curriculos c
        LEFT JOIN area_atuacao a ON c.area_atuacao_id = a.id
        WHERE c.id = ?
    ");

        $stmt->execute([$curriculoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getPerguntasAleatorias(int $areaId, int $limit = 5): array
{
    $stmt = $this->pdo->prepare("
        SELECT p.*, n.nome as nivel
        FROM perguntas p
        JOIN nivel n ON p.nivel_id = n.id
        WHERE p.area_atuacao_id = :areaId AND p.ativa = 1
        ORDER BY RAND()
        LIMIT :limit
    ");
    
    // Usando bindParam para garantir os tipos corretos
    $stmt->bindParam(':areaId', $areaId, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function verificarResposta($perguntaId, $resposta)
    {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as correct
            FROM perguntas
            WHERE id = ? AND resposta_correta = ?
        ");

        $stmt->execute([$perguntaId, $resposta]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['correct'] > 0;
    }

    public function updateUserScore($userId, $correct, $total)
    {
        $percentage = round(($correct / $total) * 100);

        $stmt = $this->pdo->prepare("
            UPDATE curriculos
            SET avaliacao = ?, total_perguntas = ?
            WHERE usuario_id = ?
        ");

        return $stmt->execute([$percentage, $total, $userId]);
    }

    public function getPerguntasPaginadas($limit, $offset)
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, a.nome AS area_nome, n.nome AS nivel_nome
            FROM perguntas p
            JOIN area_atuacao a ON p.area_atuacao_id = a.id
            JOIN nivel n ON p.nivel_id = n.id
            ORDER BY p.id DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPerguntas()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM perguntas");
        return $stmt->fetchColumn();
    }

    public function togglePerguntaStatus($perguntaId)
    {
        $stmt = $this->pdo->prepare("UPDATE perguntas SET ativa = NOT ativa WHERE id = ?");
        return $stmt->execute([$perguntaId]);
    }

    public function deletePergunta($perguntaId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM perguntas WHERE id = ?");
        return $stmt->execute([$perguntaId]);
    }

    public function addPergunta($dados)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO perguntas
            (pergunta, resposta_correta, alternativa1, alternativa2, alternativa3, area_atuacao_id, nivel_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $dados['pergunta'],
            $dados['resposta_correta'],
            $dados['alternativa1'],
            $dados['alternativa2'],
            $dados['alternativa3'],
            $dados['area_atuacao_id'],
            $dados['nivel_id'],
        ]);
    }

    public function getNiveisDificuldade(): array
    {
        return $this->pdo->query("SELECT * FROM nivel")->fetchAll(PDO::FETCH_ASSOC);
    }
}
