<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/UserModel.php';

// Inicia sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userModel = new UserModel($pdo);
    }

    public function register()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_geral'] = "Método inválido";
            header('Location: ' . BASE_URL . 'cadastro.php');
            exit();
        }

        try {
            $userData = [
                'nome' => trim($_POST['nome']),
                'cpf' => preg_replace('/[^0-9]/', '', $_POST['cpf']),
                'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
                'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? ''),
                'cep' => preg_replace('/[^0-9]/', '', $_POST['cep']),
                'complemento' => $_POST['complemento'] ?? '',
                'escolaridade' => trim($_POST['escolaridade']),
                'resumo' => trim($_POST['resumo']),
                'senha' => $_POST['senha'], 
                'linkedin' => trim($_POST['linkedin']),
                'github' => trim($_POST['github']),
                'experiencias' => trim($_POST['experiencias'] ?? '')
            ];

            // Validação
            $errors = [];
            if (empty($userData['nome'])) $errors[] = "Nome é obrigatório";
            if (empty($userData['cpf']) || strlen($userData['cpf']) !== 11) $errors[] = "CPF inválido";
            // Adicione outras validações conforme necessário

            if (!empty($errors)) {
                $_SESSION['erros_cadastro'] = $errors;
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . BASE_URL . 'cadastro.php');
                exit();
            }

            if ($this->userModel->cpfExists($userData['cpf'])) {
                $_SESSION['erros_cadastro'] = ["Este CPF já está cadastrado"];
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . BASE_URL . 'curriculo.php');
                exit();
            }

            // Verifica se email já existe
            if ($this->userModel->emailExists($userData['email'])) {
                $_SESSION['erros_cadastro'] = ["Este e-mail já está cadastrado"];
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . BASE_URL . 'curriculo.php');
                exit();
            }

            // Cria o usuário
            $userId = $this->userModel->createUser($userData);

            if (!$userId) {
                throw new Exception("Falha ao criar usuário");
            }

            $_SESSION['usuario_id'] = $userId;
            $_SESSION['usuario_nome'] = $userData['nome'];
            
            header('Location: ' . BASE_URL . 'index.php');
            exit();

        } catch (Exception $e) {
            error_log("Erro no registro: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao cadastrar. Tente novamente.";
            header('Location: ' . BASE_URL . 'cadastro.php');
            exit();
        }
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}

// Processa a ação se chamado diretamente
if (isset($_GET['action'])) {
    $controller = new AuthController();
    $action = $_GET['action'];
    
    if ($action === 'register') {
        $controller->register();
    } elseif ($action === 'logout') {
        $controller->logout();
    }
}

