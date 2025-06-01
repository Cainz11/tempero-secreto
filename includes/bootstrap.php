<?php
// Configurações do site
require_once __DIR__ . '/../config.php';

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 em produção com HTTPS

// Iniciar a sessão
session_start();

// Funções utilitárias
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/notifications.php';
require_once __DIR__ . '/recipe_actions.php';
require_once __DIR__ . '/admin_functions.php';

// Verificar se o usuário está logado
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Se o usuário não existir mais, fazer logout
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['is_admin']);
    } else {
        // Atualizar a sessão com os dados mais recentes
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['is_admin'] = $user['is_admin'] == 1;
    }
}

// Definir rota padrão
if (!isset($_GET['route'])) {
    $_GET['route'] = 'home';
}

// Verificar redirecionamentos antes de qualquer saída
function checkRedirects() {
    global $route;
    
    // Verificar se o usuário está logado para rotas protegidas
    $public_routes = ['home', 'login', 'register', 'view_recipe', 'feed'];
    if (!in_array($route, $public_routes) && !isLoggedIn()) {
        redirect(SITE_URL . '?route=login');
    }

    // Verificar se o usuário é admin para rotas administrativas
    $admin_routes = ['admin', 'manage_recipes', 'manage_comments', 'manage_categories', 'manage_approvals'];
    if (in_array($route, $admin_routes) && !isAdmin()) {
        redirect(SITE_URL . '?route=home');
    }

    // Processar qualquer redirecionamento pendente
    if (isset($_SESSION['redirect'])) {
        $redirect = $_SESSION['redirect'];
        unset($_SESSION['redirect']);
        redirect($redirect);
    }
} 