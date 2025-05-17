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
            'titulo' => 'Bem-vindo à Curriculum Premium',
            'descricao' => 'Conectamos talentos às maiores empresas do país.',
            'areas' => $areas 
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
        $areaFiltro = $_GET['area'] ?? null;
        $paginaAtual = max((int)($_GET['pagina'] ?? 1), 1);
        $itensPorPagina = 10;
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        $areas = $this->model->getAreasAtuacao();
        $resultados = [];
        $totalPaginas = 1;

        if ($areaFiltro) {
            $totalRegistros = $this->model->countCurriculosPorArea($areaFiltro);
            $resultados = $this->model->getCurriculosPorArea($areaFiltro, $itensPorPagina, $offset);
        } else {
            $totalRegistros = $this->model->countTodosCurriculos();
            $resultados = $this->model->getTodosCurriculos($itensPorPagina, $offset);
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
        $id = (int)$id;
        if ($id <= 0) {
            throw new Exception("ID inválido");
        }

        $curriculo = $this->model->getCurriculoById($id);
        if (!$curriculo) {
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
        $email = strtolower(trim($_POST['email']));
        $senha = $_POST['senha'];

        $usuario = $this->model->getUserByEmail($email);

        if ($usuario && $this->verificarSenha($senha, $usuario['senha'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];

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

        if (!empty($_SESSION['erros_cadastro'])) {
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "?action=cadastro");
            exit();
        }

        $userId = $this->model->createUser($dados);

        if ($userId) {
            $_SESSION['id'] = $userId;
            $_SESSION['nome'] = $dados['nome'];
            // Redireciona para a visualização do currículo
            header("Location: " . BASE_URL . "?action=curriculo&id=" . $userId);
            exit();
        } else {
            throw new Exception("Falha ao criar usuário");
        }
    }

    // Métodos auxiliares
    private function verificarSenha($senha, $hash)
    {
        if (strlen($hash) === 64 && ctype_xdigit($hash)) {
            return hash('sha256', $senha) === $hash;
        }
        return password_verify($senha, $hash);
    }

    private function validarDadosCadastro($post)
    {
        $dados = [
            'nome' => trim($post['nome']),
            'cpf' => preg_replace('/[^0-9]/', '', $post['cpf']),
            'email' => filter_var(trim($post['email']), FILTER_SANITIZE_EMAIL),
            'telefone' => preg_replace('/[^0-9]/', '', $post['telefone'] ?? ''),
            'cep' => preg_replace('/[^0-9]/', '', $post['cep']),
            'complemento' => $post['complemento'] ?? '',
            'escolaridade' => $post['escolaridade'],
            'resumo' => trim($post['resumo']),
            'area_atuacao_id' => $post['area_atuacao_id'] ?? null,
            'experiencias' => trim($post['experiencias'] ?? 'Sem experiência'),
            'senha' => $post['senha'],
            'confirmar_senha' => $post['confirmar_senha'] ?? '',
            'linkedin' => trim($post['linkedin'] ?? ''),
            'github' => trim($post['github'] ?? '')
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
        if (strlen($dados['cpf']) !== 11) $erros[] = "CPF inválido";
        if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) $erros[] = "E-mail inválido";
        if (strlen($dados['senha']) < 8) $erros[] = "Senha deve ter no mínimo 8 caracteres";
        if ($dados['senha'] !== $dados['confirmar_senha']) $erros[] = "As senhas não coincidem";
        if (empty($dados['area_atuacao_id'])) {
            $erros[] = "Área de atuação é obrigatória";
        }
        if (!empty($erros)) {
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
        if (!isset($_SESSION['id'])) {
            header("Location: " . BASE_URL . "?action=login");
            exit();
        }

        $dados = $this->validarDadosCadastro($_POST);
        $dados['id'] = $_SESSION['id'];

        if (!empty($_SESSION['erros_cadastro'])) {
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
            'codigo' => $codigo,
            'mensagem' => $mensagem,
            'titulo' => 'Erro ' . $codigo
        ];

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . '404.php';
        require VIEWS_PATH . 'footer.php';
        exit();
    }
}
