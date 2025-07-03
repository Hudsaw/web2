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
        error_log("Exibindo tela de login");
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }

        $data = [
            'error' => $_SESSION['login_error'] ?? null,
        ];

        $this->render('login', $data);
        unset($_SESSION['login_error']);
    }

    public function login()
    {
        error_log("Tentativa de login");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        $email    = strtolower(trim($_POST['email']));
        $password = $_POST['senha'];

        $user = $this->userModel->authenticate($email, $password);

        if ($user) {
            $_SESSION['user_id']   = $user['id'];
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
        error_log("Exibindo tela de cadastro");
        $data = [
            'errors' => $_SESSION['registrar_errors'] ?? [],
            'old'    => $_SESSION['registrar_data'] ?? [],
            'areas'  => $this->userModel->getAreasAtuacao(),
        ];

        $this->render('cadastro', $data);
        unset($_SESSION['registrar_errors'], $_SESSION['registrar_data']);
    }

    public function registrar()
    {
        error_log("Tentativa de registro");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/registrar');
        }

        $data = $this->validateUserData($_POST, false);

        if (isset($data['errors'])) {
            $_SESSION['registrar_errors'] = $data['errors'];
            $_SESSION['registrar_data']   = $_POST;
            $this->redirect('/cadastro');
        }

        $userId = $this->userModel->createUser($data);

        if ($userId) {
            $_SESSION['user_id']   = $userId;
            $_SESSION['user_name'] = $data['nome'];
            $this->redirect('/');
        }

        $_SESSION['registrar_errors'] = ['Falha ao criar usuário'];
        $this->redirect('/cadastro');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/');
    }

    public function atualizar()
    {
        error_log("Tentativa de atualizacao de curriculo");
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/editar');
        }

        $userId = $_SESSION['user_id'];
        $data   = $this->validateUserData($_POST, true);

        if (isset($data['errors'])) {
            $_SESSION['register_errors'] = $data['errors'];
            $this->redirect('/editar');
        }

        $success = $this->userModel->updateUser($userId, $data);

        if ($success) {
            $_SESSION['success_message'] = 'Currículo atualizado com sucesso!';
            $this->redirect('/');
        }

        $_SESSION['register_errors'] = ['Erro ao atualizar o currículo'];
        $this->redirect('/editar');
    }

    // Métodos Auxiliares

    private function validateUserData($post, $isUpdate = false)
    {
        $errors = [];
        $data   = [
            'nome'            => trim($post['nome'] ?? ''),
            'email'           => filter_var(trim($post['email'] ?? ''), FILTER_SANITIZE_EMAIL),
            'telefone'        => trim($post['telefone'] ?? ''),
            'cpf'             => trim($post['cpf'] ?? ''),
            'cep'             => trim($post['cep'] ?? ''),
            'complemento'     => trim($post['complemento'] ?? ''),
            'area_atuacao_id' => $post['area_atuacao_id'] ?? null,
            'escolaridade'    => $post['escolaridade'] ?? null,
            'resumo'          => trim($post['resumo'] ?? ''),
            'experiencias'    => trim($post['experiencias'] ?? ''),
            'linkedin'        => trim($post['linkedin'] ?? ''),
            'github'          => trim($post['github'] ?? ''),
            'senha'           => $post['senha'] ?? '',
            'confirmar_senha' => $post['confirmar_senha'] ?? '',    
        ];

        if (empty($data['nome'])) {
            $errors['nome'] = 'Nome é obrigatório';
        }
        if (strlen($data['senha']) < 8) {
            $errors['senha'] = 'Senha deve ter pelo menos 8 caracteres';
        } elseif ($data['senha'] !== $data['confirmar_senha']) {
            $errors['confirmar_senha'] = 'Senhas não coincidem';
        }

        if (! $isUpdate) {
            if (!$isUpdate) {
        if (empty($data['cpf'])) {
            $errors['cpf'] = 'CPF é obrigatório';
        } elseif (!preg_match('/^\d{11}$/', $data['cpf'])) {
            $errors['cpf'] = 'CPF deve conter exatamente 11 dígitos numéricos';
        } elseif ($this->userModel->cpfExists($data['cpf'])) {
            $errors['cpf'] = 'CPF já cadastrado';
        }
    }

            if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido';
            } elseif ($this->userModel->emailExists($data['email'])) {
                $errors['email'] = 'Email já cadastrado';
            }
        } else {
            if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email inválido';
            }
        }

        if (! empty($errors)) {
            return ['errors' => $errors];
        }

        return $data;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    private function redirect($url)
    {
        $baseUrl = rtrim(BASE_URL, '/') . '/';
        $path = ltrim($url, '/');
        header("Location: " . $baseUrl . $path);
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
