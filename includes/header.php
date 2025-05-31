<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- jQuery (necessário para alguns componentes Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <script>
        // Garantir que os dropdowns funcionem
        document.addEventListener('DOMContentLoaded', function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <i class="fas fa-utensils"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>?route=feed">Receitas</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Categorias
                        </a>
                        <ul class="dropdown-menu">
                            <?php
                            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
                            while ($category = $stmt->fetch()) {
                                echo '<li><a class="dropdown-item" href="' . SITE_URL . '?route=feed&category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>?route=admin">
                                    <i class="fas fa-cog"></i> Painel Admin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo SITE_URL; ?>?route=manage_approvals">
                                    <i class="fas fa-tasks"></i> Aprovações Pendentes
                                    <?php
                                    // Buscar contagem de itens pendentes
                                    $pending_query = $pdo->query("
                                        SELECT 
                                            (SELECT COUNT(*) FROM recipes WHERE status = 'pending') +
                                            (SELECT COUNT(*) FROM comments WHERE status = 'pending') as total_pending
                                    ");
                                    $pending_count = $pending_query->fetchColumn();
                                    if ($pending_count > 0):
                                    ?>
                                    <span class="badge bg-danger"><?php echo $pending_count; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Notificações -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php
                                $unread_notifications = [];
                                if (isLoggedIn()) {
                                    $unread_notifications = getUnreadNotifications($_SESSION['user_id']);
                                }
                                if (count($unread_notifications) > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo count($unread_notifications); ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0" aria-labelledby="notificationsDropdown" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <div class="p-2 border-bottom">
                                    <h6 class="mb-0">Notificações</h6>
                                </div>
                                <?php if (empty($unread_notifications)): ?>
                                    <div class="p-3 text-center text-muted">
                                        <small>Nenhuma notificação não lida</small>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($unread_notifications as $notification): ?>
                                        <div class="notification-item p-2 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <?php if ($notification['type'] == 'like'): ?>
                                                    <div class="me-2 text-danger">
                                                        <i class="fas fa-heart"></i>
                                                    </div>
                                                <?php elseif ($notification['type'] == 'comment'): ?>
                                                    <div class="me-2 text-primary">
                                                        <i class="fas fa-comment"></i>
                                                    </div>
                                                <?php elseif ($notification['type'] == 'success'): ?>
                                                    <div class="me-2 text-success">
                                                        <i class="fas fa-check-circle"></i>
                                                    </div>
                                                <?php elseif ($notification['type'] == 'warning'): ?>
                                                    <div class="me-2 text-warning">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="p-2 text-center">
                                        <a href="<?php echo SITE_URL; ?>?route=notifications" class="small text-decoration-none">
                                            Ver todas as notificações
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>

                        <!-- Menu do usuário -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=profile">
                                        <i class="fas fa-user-circle"></i> Meu Perfil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=my_recipes">
                                        <i class="fas fa-book"></i> Minhas Receitas
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>?route=settings">
                                        <i class="fas fa-cog"></i> Configurações
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>?route=logout">
                                        <i class="fas fa-sign-out-alt"></i> Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>?route=login">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>?route=register">
                                <i class="fas fa-user-plus"></i> Registrar
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <?php displayMessages(); ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 