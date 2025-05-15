<?php

require_once __DIR__ . '/database.php';

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

        error_log("iniciando registro");
        // Verifica se é uma requisição POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['erro_geral'] = "Método inválido";
            header('Location: ' . 'cadastro.php');
            exit();
        }

        error_log("antes try");
        try {

            // Processa os dados do formulário
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
                'confirmar_senha' => $_POST['confirmar_senha']
            ];
            error_log("vai validar");
            // Validação
            $errors = $this->validateRegistration($userData);

            if (!empty($errors)) {
                $_SESSION['erros_cadastro'] = $errors;
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . 'cadastro.php');
                exit();
            }

            // Verifica se email já existe
            if ($this->userModel->emailExists($userData['email'])) {
                $_SESSION['erros_cadastro'] = ["Este e-mail já está cadastrado"];
                $_SESSION['dados_cadastro'] = $userData;
                header('Location: ' . 'cadastro.php');
                exit();
            }

            // Cria o usuário
            $userId = $this->userModel->createUser($userData);

            if (!$userId) {
                throw new Exception("Falha ao criar usuário no banco de dados");
            }

            $_SESSION['usuario_id'] = $userId;
            $_SESSION['usuario_nome'] = $userData['nome'];

            header('Location: ' . 'index.php');
            exit();
        } catch (PDOException $e) {
            error_log("Erro PDO no registro: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro no banco de dados. Por favor, tente novamente.";
            header('Location: '. 'cadastro.php');
            exit();
        } catch (Exception $e) {
            error_log("Erro geral no registro: " . $e->getMessage());
            $_SESSION['erro_geral'] = "Erro ao processar cadastro. Por favor, tente novamente.";
            header('Location: ' . 'cadastro.php');
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

        if (empty($_POST['senha']) || strlen($_POST['senha']) < 8) {
            $errors[] = "Senha deve ter pelo menos 8 caracteres";
        }

        if ($_POST['senha'] !== $_POST['confirmar_senha']) {
            $errors[] = "As senhas não coincidem";
        }

        return $errors;
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
        header("Location: " . "/index.php");
        exit();
    }
}
