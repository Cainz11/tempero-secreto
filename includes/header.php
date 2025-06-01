<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tempero Secreto - Compartilhe e descubra receitas deliciosas">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">

    <!-- jQuery primeiro -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Depois Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Skip to main content link -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" role="navigation" aria-label="Menu principal">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>" aria-label="<?php echo SITE_NAME; ?> - Página inicial">
                <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar menu de navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>?route=feed" aria-label="Explorar receitas">
                            <i class="fas fa-search me-1" aria-hidden="true"></i>Explorar
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>?route=add_recipe" aria-label="Adicionar nova receita">
                                <i class="fas fa-plus me-1" aria-hidden="true"></i>Nova Receita
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu do usuário">
                                <i class="fas fa-user me-1" aria-hidden="true"></i><?php echo $_SESSION['user_name']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=profile" aria-label="Acessar meu perfil">
                                        <i class="fas fa-user-circle me-2" aria-hidden="true"></i>Meu Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=my_recipes" aria-label="Ver minhas receitas">
                                        <i class="fas fa-book me-2" aria-hidden="true"></i>Minhas Receitas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=settings" aria-label="Acessar configurações">
                                        <i class="fas fa-cog me-2" aria-hidden="true"></i>Configurações
                                    </a>
                                </li>
                                <?php if (isAdmin()): ?>
                                <li><hr class="dropdown-divider" role="separator"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=admin" aria-label="Acessar painel administrativo">
                                        <i class="fas fa-tachometer-alt me-2" aria-hidden="true"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=manage_recipes" aria-label="Gerenciar todas as receitas">
                                        <i class="fas fa-book me-2" aria-hidden="true"></i>Gerenciar Receitas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=manage_categories" aria-label="Gerenciar categorias">
                                        <i class="fas fa-folder me-2" aria-hidden="true"></i>Gerenciar Categorias
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=manage_users" aria-label="Gerenciar usuários">
                                        <i class="fas fa-users me-2" aria-hidden="true"></i>Gerenciar Usuários
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider" role="separator"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>?route=logout" aria-label="Sair do sistema">
                                        <i class="fas fa-sign-out-alt me-2" aria-hidden="true"></i>Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>?route=login" aria-label="Fazer login">
                                <i class="fas fa-sign-in-alt me-1" aria-hidden="true"></i>Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>?route=register" aria-label="Criar nova conta">
                                <i class="fas fa-user-plus me-1" aria-hidden="true"></i>Registrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container" role="alert" aria-live="polite">
        <?php displayMessages(); ?>
    </div>

    <!-- Main Content -->
    <main id="main-content" class="main-container" role="main">

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar todos os dropdowns do Bootstrap
        var dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(function(dropdown) {
            new bootstrap.Dropdown(dropdown);
        });
    });
    </script>

    <!-- Adicionar estilos de acessibilidade -->
    <style>
    /* Skip Link - Link para pular para o conteúdo principal */
    .skip-link {
        background: #000;
        color: #fff;
        font-weight: 700;
        left: 50%;
        padding: 4px;
        position: absolute;
        transform: translateY(-100%);
        transition: transform 0.3s;
    }

    .skip-link:focus {
        transform: translateY(0%);
    }

    /* Alto contraste para textos pequenos */
    .small-text {
        font-size: 1rem;
        line-height: 1.5;
    }

    /* Melhorar visibilidade do foco */
    a:focus,
    button:focus,
    input:focus,
    select:focus,
    textarea:focus {
        outline: 3px solid #4A90E2;
        outline-offset: 2px;
    }

    /* Aumentar área clicável */
    .nav-link,
    .btn,
    .dropdown-item {
        padding: 0.75rem 1rem;
    }

    /* Melhorar contraste */
    .text-muted {
        color: #666 !important;
    }

    /* Garantir que textos sejam legíveis */
    body {
        font-size: 16px;
        line-height: 1.5;
    }

    /* Melhorar espaçamento para facilitar leitura */
    p {
        margin-bottom: 1.5em;
    }
    </style>
</body>
</html> 