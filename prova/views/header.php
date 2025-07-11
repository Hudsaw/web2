<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Curriculum Quiz' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="light-theme">
    
    <header>
        <div class="container">
            <div class="header-container">
                <nav class="secao">
                    <div class="logo">
                        <div>
                            <img src="https://img.icons8.com/?size=100&id=45588&format=png&color=FFFFFF" alt="Logo" class="logo-img" />
                        </div>
                        <div id="espacador">.</div>
                        <div>
                            <a href="<?= BASE_URL ?>" class="logo-text">Curriculum Quiz</a>
                        </div>
                    </div>

                    <nav class="nav-user">
                        <div class="user-greeting">
                            <span>Olá, <?= htmlspecialchars($nomeUsuario ?? 'Visitante') ?>!</span>
                        </div>
                        <div class="user-actions">
                            <div>
                                <button class="theme-switcher"></button>
                            </div>

                            <?php if ($usuarioLogado ?? false): ?>
        <?php 
            $user = $this->getCurrentUser();
            $score = 0;
            $totalQuestions = $user['total_perguntas'] ?? 0;
            
            if ($totalQuestions > 0) {
                $score = round(($user['avaliacao'] / $totalQuestions) * 100);
            }
        ?>
        <span class="user-score">
            <?= $score ?>% acertos
        </span>
    <?php endif; ?>
                            <div>
                                <?php if ($usuarioLogado ?? false): ?>
                                    <a href="<?= BASE_URL ?>/logout" class="btn-logout">
                                        <span class="btn-text">Sair</span>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/login" class="btn-login">
                                        <span class="btn-text">Login</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </nav>
                </nav>
            </div>
    </header>