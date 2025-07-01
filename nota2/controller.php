<?php
require_once __DIR__ . '/model.php';

class MainController
{
    private $model;

    public function __construct()
    {
        $this->model = new Model();
    }

    public function home()
    {
        try {
            $areas = $this->model->getAreasAtuacao();

            $dados = [
                'titulo'    => 'Bem-vindo à Curriculum Quiz',
                'descricao' => 'Conectamos talentos às maiores empresas do país.',
                'areas'     => $areas,
                'usuario'   => isset($_SESSION['id']) ? $this->model->getUserById($_SESSION['id']) : null,
            ];

            require VIEWS_PATH . 'header.php';
            require VIEWS_PATH . 'home.php';
            require VIEWS_PATH . 'footer.php';

        } catch (Exception $e) {
            error_log("Erro no Controller home: " . $e->getMessage());
        }
    }

    public function buscarCurriculos()
    {
        try {
            $areaFiltro     = $_GET['area'] ?? null;
            $paginaAtual    = max((int) ($_GET['pagina'] ?? 1), 1);
            $itensPorPagina = 10;
            $offset         = ($paginaAtual - 1) * $itensPorPagina;

            $areas        = $this->model->getAreasAtuacao();
            $resultados   = [];
            $totalPaginas = 1;

            if ($areaFiltro) {
                $totalRegistros = $this->model->countCurriculosPorArea($areaFiltro);
                $resultados     = $this->model->getCurriculosPorArea($areaFiltro, $itensPorPagina, $offset);
            } else {
                $totalRegistros = $this->model->countTodosCurriculos();
                $resultados     = $this->model->getTodosCurriculos($itensPorPagina, $offset);
            }

            $totalPaginas = ceil($totalRegistros / $itensPorPagina);

            require VIEWS_PATH . 'header.php';
            require VIEWS_PATH . 'busca.php';
            require VIEWS_PATH . 'footer.php';

        } catch (Exception $e) {
            error_log("Erro na busca: " . $e->getMessage());
            header("HTTP/1.1 500 Internal Server Error");
            require VIEWS_PATH . '404.php';
        }
    }

    //Curriculo
    public function verCurriculo($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            throw new Exception("ID inválido");
        }

        $curriculo = $this->model->getCurriculoById($id);
        if (! $curriculo) {
            throw new Exception("Currículo não encontrado");
        }

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . 'curriculo.php';
        require VIEWS_PATH . 'footer.php';
    }

    // Login
    public function mostrarLogin()
    {
        $erro = $_SESSION['erro_login'] ?? null;
        unset($_SESSION['erro_login']);

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . 'login.php';
        require VIEWS_PATH . 'footer.php';
    }

    public function processarLogin()
{
    error_log("processarLogin");
    $email = strtolower(trim($_POST['email']));
    $senha = $_POST['senha'];

    $usuario = $this->model->getUserByEmail($email);
    
    error_log("Usuário encontrado: " . print_r($usuario, true));
    error_log("Senha fornecida: " . $senha);
    error_log("Hash armazenado: " . ($usuario['senha'] ?? 'N/A'));

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo_usuario'] = $usuario['tipo'];
        $_SESSION['pontuacao'] = $usuario['avaliacao'] ?? 0;

        $redirect = $_SESSION['redirect_url'] ?? 'index.php?action=home';
        unset($_SESSION['redirect_url']);

        header("Location: " . BASE_URL . $redirect);
        exit();
    } else {
        $_SESSION['erro_login'] = "Credenciais inválidas";
        header("Location: " . BASE_URL . "?action=login");
        exit();
    }
}

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header("Location: " . BASE_URL);
        exit();
    }

    // Cadastro
    public function mostrarCadastro()
    {
        $erros = $_SESSION['erros_cadastro'] ?? [];
        $dados = $_SESSION['dados_cadastro'] ?? [];
        unset($_SESSION['erros_cadastro'], $_SESSION['dados_cadastro']);

        $areas = $this->model->getAreasAtuacao();

        // Se usuário logado, carrega dados para edição
        if (isset($_SESSION['id'])) {
            $dados = $this->model->getUserById($_SESSION['id']);
        }

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . 'cadastro.php';
        require VIEWS_PATH . 'footer.php';
    }

    public function processarCadastro()
    {
        $dados = $this->validarDadosCadastro($_POST);

        // Verifica se já existe usuário com mesmo email ou CPF
        if ($this->model->emailExists($dados['email'])) {
            $_SESSION['erros_cadastro'][] = "E-mail já cadastrado";
        }

        if ($this->model->cpfExists($dados['cpf'])) {
            $_SESSION['erros_cadastro'][] = "CPF já cadastrado";
        }

        if (! empty($_SESSION['erros_cadastro'])) {
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "?action=cadastro");
            exit();
        }

        $userId = $this->model->createUser($dados);

        if ($userId) {
            $_SESSION['id']   = $userId;
            $_SESSION['nome'] = $dados['nome'];
            // Redireciona para a visualização do currículo
            header("Location: " . BASE_URL . "?action=curriculo&id=" . $userId);
            exit();
        } else {
            throw new Exception("Falha ao criar usuário");
        }
    }

    // Métodos auxiliares

    private function validarDadosCadastro($post)
    {
        $dados = [
            'nome'            => trim($post['nome']),
            'cpf'             => preg_replace('/[^0-9]/', '', $post['cpf']),
            'email'           => filter_var(trim($post['email']), FILTER_SANITIZE_EMAIL),
            'telefone'        => preg_replace('/[^0-9]/', '', $post['telefone'] ?? ''),
            'cep'             => preg_replace('/[^0-9]/', '', $post['cep']),
            'complemento'     => $post['complemento'] ?? '',
            'escolaridade'    => $post['escolaridade'],
            'resumo'          => trim($post['resumo']),
            'area_atuacao_id' => $post['area_atuacao_id'] ?? null,
            'experiencias'    => trim($post['experiencias'] ?? 'Sem experiência'),
            'senha'           => $post['senha'],
            'confirmar_senha' => $post['confirmar_senha'] ?? '',
            'linkedin'        => trim($post['linkedin'] ?? ''),
            'github'          => trim($post['github'] ?? ''),
        ];

        // Validação
        if ($dados['senha'] !== $dados['confirmar_senha']) {
            $_SESSION['erros_cadastro'][] = "As senhas não coincidem";
        } elseif (strlen($dados['senha']) < 8) {
            $_SESSION['erros_cadastro'][] = "Senha deve ter no mínimo 8 caracteres";
        }
        $dados['senha'] = trim($dados['senha']);

        // Validações
        $erros = [];
        if (strlen($dados['cpf']) !== 11) {
            $erros[] = "CPF inválido";
        }

        if (! filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
            $erros[] = "E-mail inválido";
        }

        if (strlen($dados['senha']) < 8) {
            $erros[] = "Senha deve ter no mínimo 8 caracteres";
        }

        if ($dados['senha'] !== $dados['confirmar_senha']) {
            $erros[] = "As senhas não coincidem";
        }

        if (empty($dados['area_atuacao_id'])) {
            $erros[] = "Área de atuação é obrigatória";
        }
        if (! empty($erros)) {
            $_SESSION['erros_cadastro'] = $erros;
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "cadastro.php");
            exit();
        }

        unset($dados['confirmar_senha']);

        return $dados;
    }

    public function processarAtualizacao()
    {
        if (! isset($_SESSION['id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        $dados       = $this->validarDadosCadastro($_POST);
        $dados['id'] = $_SESSION['id'];

        if (! empty($_SESSION['erros_cadastro'])) {
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "?action=cadastro");
            exit();
        }

        if ($this->model->updateUser($dados)) {
            $_SESSION['nome'] = $dados['nome'];
            // Redireciona para a visualização do currículo atualizado
            header("Location: " . BASE_URL . "?action=curriculo&id=" . $dados['id']);
            exit();
        } else {
            throw new Exception("Falha ao atualizar usuário");
        }
    }

    public function mostrarErro($codigo = 404, $mensagem = 'Página não encontrada')
    {
        http_response_code($codigo);

        $dados = [
            'codigo'   => $codigo,
            'mensagem' => $mensagem,
            'titulo'   => 'Erro ' . $codigo,
        ];

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . '404.php';
        require VIEWS_PATH . 'footer.php';
        exit();
    }

    public function mostrarQuiz()
    {
        try {
            $areas = $this->model->getAreasAtuacao();

            $dados = [
                'titulo'    => 'Quiz de Conhecimento',
                'descricao' => 'Teste seus conhecimentos em diferentes áreas de tecnologia.',
                'areas'     => $areas,
            ];

            require VIEWS_PATH . 'header.php';
            require VIEWS_PATH . 'selecionar.php';
            require VIEWS_PATH . 'footer.php';
        } catch (Exception $e) {
            error_log("Erro ao mostrar quiz: " . $e->getMessage());
            $this->mostrarErro(500, "Erro ao carregar o quiz");
        }
    }

    public function iniciarQuiz()
    {
        if (! isset($_SESSION['id'])) {
            $_SESSION['redirect_url'] = '?action=jogar' . (isset($_GET['area']) ? '&area=' . $_GET['area'] : '');
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        try {

            if (! isset($_SESSION['id'])) {
                throw new Exception("Usuário não autenticado");
            }

            $areaId = $_GET['area'] ?? null;
            if (! $areaId) {
                $this->mostrarErro(400, "Área de atuação não especificada");
                return;
            }

            $perguntas = $this->model->getPerguntasAleatorias($areaId);

            if (count($perguntas) < 5) {
                throw new Exception("Não há perguntas suficientes para esta área");
            }

            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode([
                    'perguntas'      => $perguntas,
                    'totalPerguntas' => count($perguntas),
                ]);
                exit();
            } else {
                $dados = [
                    'titulo'         => 'Quiz em Andamento',
                    'perguntas'      => $perguntas,
                    'areaId'         => $areaId,
                    'totalPerguntas' => count($perguntas),
                    'perguntaAtual'  => 0,
                ];

                require VIEWS_PATH . 'header.php';
                require VIEWS_PATH . 'quiz.php';
                require VIEWS_PATH . 'footer.php';
            }
        } catch (Exception $e) {
            error_log("Erro ao iniciar quiz: " . $e->getMessage());
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
                exit();
            } else {
                $this->mostrarErro(500, "Erro ao iniciar o quiz");
            }
        }
    }

    private function isAjaxRequest()
    {
        return ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function processarQuiz()
    {
        header('Content-Type: application/json');

        if (! isset($_SESSION['id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autenticado']);
            exit();
        }

        try {
            $userId = $_SESSION['id'];
            $data   = json_decode(file_get_contents('php://input'), true);

            if (! $data || ! isset($data['respostas']) || ! isset($data['perguntaIds'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Dados inválidos']);
                exit();
            }

            $respostas   = $data['respostas'];
            $perguntaIds = $data['perguntaIds'];

            if (count($respostas) !== 5 || count($perguntaIds) !== 5) {
                http_response_code(400);
                echo json_encode(['error' => 'Número inválido de respostas']);
                exit();
            }

            // Verifica respostas e calcula acertos
            $acertos = 0;
            for ($i = 0; $i < 5; $i++) {
                if ($this->model->verificarResposta($perguntaIds[$i], $respostas[$i])) {
                    $acertos++;
                }
            }

            $porcentagem = round(($acertos / 5) * 100);

            // Obter pontuação atual do usuário
            $usuario             = $this->model->getUserById($userId);
            $pontuacaoAtual      = $usuario['avaliacao'] ?? 0;
            $totalPerguntasAtual = $usuario['total_perguntas'] ?? 0;

            // Calcular nova pontuação (média ponderada)
            $novaPontuacao = round(($pontuacaoAtual * $totalPerguntasAtual + $porcentagem * 5) / ($totalPerguntasAtual + 5));
            $novoTotal     = $totalPerguntasAtual + 5;

            // Atualiza a pontuação do usuário no banco de dados
            $success = $this->model->atualizarPontuacao($userId, $novaPontuacao, $novoTotal);

            if (! $success) {
                throw new Exception("Falha ao atualizar pontuação no banco de dados");
            }

            // Atualiza a sessão com a nova pontuação
            $_SESSION['pontuacao'] = $novaPontuacao;

            echo json_encode([
                'success'       => true,
                'acertos'       => $acertos,
                'porcentagem'   => $porcentagem,
                'novaPontuacao' => $novaPontuacao,
                'redirect'      => BASE_URL . '?action=home',
            ]);
            exit();

        } catch (Exception $e) {
            error_log("Erro ao processar quiz: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
            exit();
        }
    }

    public function gerenciar()
    {
        if (! isset($_SESSION['id']) || ($_SESSION['tipo_usuario'] ?? '') != 'admin') {
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        try {
            // Configuração de paginação
            $paginaAtual    = max((int) ($_GET['pagina'] ?? 1), 1);
            $itensPorPagina = 10;
            $offset         = ($paginaAtual - 1) * $itensPorPagina;

            // Obter perguntas paginadas
            $perguntas      = $this->model->getPerguntasPaginadas($itensPorPagina, $offset);
            $totalPerguntas = $this->model->countTodasPerguntas();

            $dados = [
                'titulo'         => 'Painel Administrativo',
                'perguntas'      => $perguntas,
                'paginaAtual'    => $paginaAtual,
                'totalPaginas'   => ceil($totalPerguntas / $itensPorPagina),
                'totalPerguntas' => $totalPerguntas,
            ];

            require VIEWS_PATH . 'header.php';
            require VIEWS_PATH . 'admin.php';
            require VIEWS_PATH . 'footer.php';
        } catch (Exception $e) {
            error_log("Erro no painel admin: " . $e->getMessage());
            $this->mostrarErro(500, "Erro ao acessar painel administrativo");
        }
    }

    public function toggleStatus()
    {
        if (! isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 'admin') {
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        $perguntaId = $_GET['id'] ?? null;
        if (! $perguntaId) {
            throw new Exception("ID da pergunta não especificado");
        }

        $this->model->toggleStatusPergunta($perguntaId);

        header("Location: " . BASE_URL . "?action=admin");
        exit();
    }

    public function excluirPergunta()
    {
        if (! isset($_SESSION['id']) || $_SESSION['tipo_usuario'] != 'admin') {
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        $perguntaId = $_GET['id'] ?? null;
        if (! $perguntaId) {
            throw new Exception("ID da pergunta não especificado");
        }

        $this->model->excluirPergunta($perguntaId);

        header("Location: " . BASE_URL . "?action=admin");
        exit();
    }

    public function mostrarFormularioPergunta()
    {
        try {
            $areas  = $this->model->getAreasAtuacao();
            $niveis = $this->model->getNiveisDificuldade();

            $dados = [
                'titulo' => 'Adicionar Nova Pergunta',
                'areas'  => $areas,
                'niveis' => $niveis,
            ];

            require VIEWS_PATH . 'header.php';
            require VIEWS_PATH . 'pergunta.php';
            require VIEWS_PATH . 'footer.php';
        } catch (Exception $e) {
            error_log("Erro ao mostrar formulário: " . $e->getMessage());
            $this->mostrarErro(500, "Erro ao carregar formulário");
        }
    }

// Métodos auxiliares
    public function processarAdicaoPergunta()
    {
        error_log("Iniciando processarAdicaoPergunta");

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Método inválido");
            }

            $dados = $this->validarDadosPergunta($_POST);

            if ($this->model->adicionarPergunta($dados)) {
                $_SESSION['mensagem'] = "Pergunta adicionada com sucesso!";
                header("Location: " . BASE_URL . "?action=admin");
                exit();
            } else {
                throw new Exception("Falha ao adicionar pergunta no banco de dados");
            }
        } catch (Exception $e) {
            error_log("Erro em processarAdicaoPergunta: " . $e->getMessage());
            $_SESSION['erro']       = $e->getMessage();
            $_SESSION['dados_form'] = $_POST;
            header("Location: " . BASE_URL . "?action=adicionarPergunta");
            exit();
        }
    }

    private function validarDadosPergunta($post)
    {
        $dados = [
            'pergunta'         => trim($post['pergunta']),
            'resposta_correta' => trim($post['resposta_correta']),
            'alternativa1'     => trim($post['alternativa1']),
            'alternativa2'     => trim($post['alternativa2']),
            'alternativa3'     => trim($post['alternativa3']),
            'area_atuacao_id'  => $post['area_atuacao_id'],
            'nivel_id'         => $post['nivel_id'],
        ];

        // Validações básicas
        $erros = [];
        if (empty($dados['pergunta'])) {
            $erros[] = "Pergunta é obrigatória";
        }

        if (empty($dados['resposta_correta'])) {
            $erros[] = "Resposta correta é obrigatória";
        }

        if (empty($dados['area_atuacao_id'])) {
            $erros[] = "Área de atuação é obrigatória";
        }

        if (empty($dados['nivel_id'])) {
            $erros[] = "Nível de dificuldade é obrigatório";
        }

        if (! empty($erros)) {
            $_SESSION['erros']      = $erros;
            $_SESSION['dados_form'] = $dados;
            throw new Exception(implode("<br>", $erros));
        }

        return $dados;
    }

}
