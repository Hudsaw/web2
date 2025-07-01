<?php
class AuthController
{
    private $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function showLogin()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        $data = [
            'error' => $_SESSION['login_error'] ?? null
        ];
        
        $this->render('login', $data);
        unset($_SESSION['login_error']);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $email = strtolower(trim($_POST['email']));
        $password = $_POST['senha'];

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_role'] = $user['tipo'];

            $redirect = $_SESSION['redirect_url'] ?? '/';
            unset($_SESSION['redirect_url']);
            
            $this->redirect($redirect);
        }

        $_SESSION['login_error'] = "Credenciais inválidas";
        $this->redirect('/login');
    }

    public function showRegister()
    {
        $data = [
            'errors' => $_SESSION['register_errors'] ?? [],
            'old' => $_SESSION['register_data'] ?? [],
            'areas' => $this->userModel->getAreasAtuacao()
        ];
        
        $this->render('cadastro', $data);
        unset($_SESSION['register_errors'], $_SESSION['register_data']);
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/register');
        }

        $data = $this->validateRegistration($_POST);

        if (isset($data['errors'])) {
            $_SESSION['register_errors'] = $data['errors'];
            $_SESSION['register_data'] = $_POST;
            $this->redirect('/cadastro');
        }

        $userId = $this->userModel->createUser($data);

        if ($userId) {
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $data['nome'];
            $this->redirect('/');
        }

        $_SESSION['register_errors'] = ['Falha ao criar usuário'];
        $this->redirect('/cadastro');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    private function validateRegistration($post)
    {
        $errors = [];
        $data = [
            'nome' => trim($post['nome']),
            'email' => filter_var(trim($post['email']), FILTER_SANITIZE_EMAIL),
            'senha' => $post['senha'],
            'confirmar_senha' => $post['confirmar_senha'] ?? '',
            'area_atuacao_id' => $post['area_atuacao_id'] ?? null
        ];

        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email inválido';
        } elseif ($this->userModel->emailExists($data['email'])) {
            $errors['email'] = 'Email já cadastrado';
        }

        if (strlen($data['senha']) < 8) {
            $errors['senha'] = 'Senha deve ter pelo menos 8 caracteres';
        } elseif ($data['senha'] !== $data['confirmar_senha']) {
            $errors['confirmar_senha'] = 'Senhas não coincidem';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        unset($data['confirmar_senha']);

        return $data;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function redirect($url)
    {
        header("Location: " . BASE_URL . ltrim($url, '/'));
        exit();
    }

    private function render($view, $data = [])
    {
        extract($data);
        require VIEWS_PATH . 'header.php';
        require VIEWS_PATH . $view . '.php';
        require VIEWS_PATH . 'footer.php';
    }
}