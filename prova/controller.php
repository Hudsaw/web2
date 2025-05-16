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
        $dados = [
            'titulo' => 'Bem-vindo à Curriculum Premium',
            'descricao' => 'Conectamos talentos às maiores empresas do país.'
        ];

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . 'home.php';
        require VIEWS_PATH . 'footer.php';
    }

    public function buscarCurriculos()
    {
        $areaFiltro = $_GET['area'] ?? 0;
        $paginaAtual = max((int)($_GET['pagina'] ?? 1), 1);
        $itensPorPagina = 10;
        $offset = ($paginaAtual - 1) * $itensPorPagina;

        $areas = $this->model->getAreasAtuacao();

        if ($areaFiltro) {
            $totalRegistros = $this->model->countCurriculosPorArea($areaFiltro);
            $resultados = $this->model->getCurriculosPorArea($areaFiltro, $itensPorPagina, $offset);
            $totalPaginas = ceil($totalRegistros / $itensPorPagina);
        }

        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . 'busca.php';
        require VIEWS_PATH . 'footer.php';
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

            $redirect = $_SESSION['redirect_url'] ?? 'index.php';
            unset($_SESSION['redirect_url']);

            header("Location: " . BASE_URL . $redirect);
            exit();
        } else {
            $_SESSION['erro_login'] = "Credenciais inválidas";
            header("Location: " . BASE_URL . "login.php");
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

        if ($this->model->emailExists($dados['email'])) {
            $_SESSION['erros_cadastro'][] = "E-mail já cadastrado";
        }

        if ($this->model->cpfExists($dados['cpf'])) {
            $_SESSION['erros_cadastro'][] = "CPF já cadastrado";
        }

        if (!empty($_SESSION['erros_cadastro'])) {
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "cadastro.php");
            exit();
        }

        $userId = $this->model->createUser($dados);

        if ($userId) {
            $_SESSION['id'] = $userId;
            $_SESSION['nome'] = $dados['nome'];
            header("Location: " . BASE_URL . "index.php");
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

        // Remove campos não necessários para o banco
        unset($dados['confirmar_senha']);

        // Hash da senha
        $dados['senha'] = password_hash($dados['senha'], PASSWORD_DEFAULT);

        return $dados;
    }

    public function processarAtualizacao()
    {
        if (!isset($_SESSION['id'])) {
            header("Location: " . BASE_URL . "login.php");
            exit();
        }

        $dados = $this->validarDadosCadastro($_POST);
        $dados['id'] = $_SESSION['id'];

        if (!empty($_SESSION['erros_cadastro'])) {
            $_SESSION['dados_cadastro'] = $dados;
            header("Location: " . BASE_URL . "cadastro.php");
            exit();
        }

        if ($this->model->updateUser($dados)) {
            $_SESSION['nome'] = $dados['nome'];
            header("Location: " . BASE_URL . "curriculo.php?id=" . $dados['id']);
            exit();
        } else {
            throw new Exception("Falha ao atualizar usuário");
        }
    }
}
