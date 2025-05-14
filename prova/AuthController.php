<?php

namespace App\Controllers;

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\UserModel;
use Config\Database;
use Exception;
use PDOException;

class AuthController
{
    private $userModel;

    public function __construct()
    {
        $pdo = Database::getInstance();
        $this->userModel = new UserModel($pdo);
    }

    public function handleRequest()
    {
        // Ativar exibição de erros para depuração
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'register':
                $this->register();
                break;
            case 'showRegistrationForm':
                $this->showRegistrationForm();
                break;
            case 'logout':
                $this->logout();
                break;
            default:
                $_SESSION['erro_geral'] = "Ação inválida";
                header('Location: ' . BASE_URL . 'public/index.php');
                exit();
        }
    }

    public function showRegistrationForm()
    {
        require_once __DIR__ . '/../../config/constants.php';
        $planoSelecionado = $_GET['plano'] ?? null;
        require_once __DIR__ . '/../../views/auth/cadastro.php';
    }

    public function register()
    {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verifica se é uma requisição POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_geral'] = "Método inválido";
            header('Location: ' . BASE_URL . 'views/auth/cadastro.php');
            exit();
        }

        try {
            // Processa os dados do formulário
            $userData = [
                'nome' => trim($_POST['nome']),
                'cpf' => preg_replace('/[^0-9]/', '', $_POST['cpf']),
                'email' => filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL),
                'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? ''),
                'cep' => preg_replace('/[^0-9]/', '', $_POST['cep']),
                'complemento' => $_POST['numero'] ?? '',
                'tipo' => $_POST['tipo_usuario'],
                'especialidade_id' => $_POST['especialidade_id'],
                'crm' => ($_POST['tipo_usuario'] === 'medico') ? $_POST['crm'] : null,
                'plano_id' => 1,
                'senha' => $_POST['senha'],
                'confirmar_senha' => $_POST['confirmar_senha']
            ];

            // Validação
            $errors = $this->validateRegistration($userData);

            if (!empty($errors)) {
                $_SESSION['erros_cadastro'] = $errors;
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . BASE_URL . 'views/auth/cadastro.php');
                exit();
            }

            // Verifica se email já existe
            if ($this->userModel->emailExists($userData['email'])) {
                $_SESSION['erros_cadastro'] = ["Este e-mail já está cadastrado"];
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . BASE_URL . 'views/auth/cadastro.php');
                exit();
            }

            // Cria o usuário
            $userId = $this->userModel->createUser($userData);

            if (!$userId) {
                throw new Exception("Falha ao criar usuário no banco de dados");
            }

            $_SESSION['usuario_id'] = $userId;
            $_SESSION['usuario_nome'] = $userData['nome'];
            $_SESSION['tipo_usuario'] = $userData['tipo'];

            // Redireciona conforme o tipo de usuário
            switch ($userData['tipo']) {
                case 'admin':
                    $redirectUrl = 'views/admin/dashboard.php';
                    break;
                case 'medico':
                    $redirectUrl = 'views/medico/dashboard.php';
                    break;
                case 'especialista':
                    $redirectUrl = 'views/especialista/dashboard.php';
                    break;
                default:
                    $redirectUrl = 'public/index.php';
            }

            header('Location: ' . BASE_URL . $redirectUrl);
            exit();
        } catch (PDOException $e) {
            error_log("Erro PDO no registro: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro no banco de dados. Por favor, tente novamente.";
            header('Location: ' . BASE_URL . 'views/auth/cadastro.php');
            exit();
        } catch (Exception $e) {
            error_log("Erro geral no registro: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao processar cadastro. Por favor, tente novamente.";
            header('Location: ' . BASE_URL . 'views/auth/cadastro.php');
            exit();
        }
    }

    private function validateRegistration($data)
    {
        $errors = [];

        if (empty($data['nome']) || strlen($data['nome']) < 3) {
            $errors[] = "Nome deve ter pelo menos 3 caracteres";
        }

        if (empty($data['telefone']) || (strlen($data['telefone']) < 10 || strlen($data['telefone']) > 11)) {
            $errors[] = "Telefone deve conter 10 ou 11 dígitos";
        }

        if (empty($data['cpf']) || strlen($data['cpf']) !== 11) {
            $errors[] = "CPF deve conter 11 dígitos";
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "E-mail inválido";
        }

        if (empty($data['cep']) || strlen($data['cep']) !== 8) {
            $errors[] = "CEP deve conter 8 dígitos";
        }

        $tiposPermitidos = ['medico', 'especialista'];
        if (empty($data['tipo']) || !in_array($data['tipo'], $tiposPermitidos)) {
            $errors[] = "Selecione um tipo de usuário válido";
        }

        if (empty($data['especialidade_id'])) {
            $errors[] = "Selecione uma especialidade";
        }

        if ($data['tipo'] === 'medico' && empty($data['plano_id'])) {
            $errors[] = "Selecione um plano";
        }

        if (empty($_POST['senha']) || strlen($_POST['senha']) < 8) {
            $errors[] = "Senha deve ter pelo menos 8 caracteres";
        }

        if ($_POST['senha'] !== $_POST['confirmar_senha']) {
            $errors[] = "As senhas não coincidem";
        }

        return $errors;
    }

    public static function checkAccess($allowedRoles)
    {
        if (!isset($_SESSION['usuario_id'])) {
            return false;
        }
        return in_array($_SESSION['tipo_usuario'], $allowedRoles);
    }

    public function logout()
    {
        // Inicia a sessão se não estiver ativa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpa todos os dados da sessão
        $_SESSION = array();

        // Destrói a sessão
        session_destroy();

        // Redireciona para a página inicial
        header("Location: " . BASE_URL . "public/index.php");
        exit();
    }
}

// Só instancia se for chamado diretamente
if (isset($_GET['action'])) {
    $controller = new AuthController();
    $controller->handleRequest();
}