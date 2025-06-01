<?php

// Rotas públicas
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Rotas que requerem autenticação
$auth_routes = [
    'profile',
    'my_recipes',
    'notifications',
    'edit_recipe',
    'add_recipe'
];

// Rotas que requerem privilégios de administrador
$admin_routes = [
    'manage_recipes',
    'manage_categories',
    'manage_users',
    'manage_comments',
    'manage_approvals',
    'site_settings'
];

// Verificar autenticação para rotas protegidas
if (in_array($route, $auth_routes) && !isLoggedIn()) {
    setMessage('warning', 'Você precisa estar logado para acessar esta página.');
    redirect(SITE_URL . '?route=login');
}

// Verificar privilégios de administrador
if (in_array($route, $admin_routes) && !isAdmin()) {
    setMessage('danger', 'Você não tem permissão para acessar esta página.');
    redirect(SITE_URL);
}

// Redirecionar rota antiga do admin para o perfil
if ($route === 'admin') {
    redirect(SITE_URL . '?route=profile');
}

// Roteamento
switch ($route) {
    case 'home':
        include 'pages/home.php';
        break;
        
    case 'login':
        include 'pages/login.php';
        break;
        
    case 'register':
        include 'pages/register.php';
        break;
        
    case 'feed':
        include 'pages/feed.php';
        break;
        
    case 'view_recipe':
        include 'pages/view_recipe.php';
        break;
        
    case 'profile':
        include 'pages/profile.php';
        break;
        
    case 'my_recipes':
        include 'pages/my_recipes.php';
        break;
        
    case 'notifications':
        include 'pages/notifications.php';
        break;
        
    case 'edit_recipe':
        include 'pages/edit_recipe.php';
        break;
        
    case 'add_recipe':
        include 'pages/add_recipe.php';
        break;
        
    // Rotas administrativas
    case 'manage_recipes':
        include 'pages/admin/manage_recipes.php';
        break;
        
    case 'manage_categories':
        include 'pages/admin/manage_categories.php';
        break;
        
    case 'manage_users':
        include 'pages/admin/manage_users.php';
        break;
        
    case 'manage_comments':
        include 'pages/admin/manage_comments.php';
        break;
        
    case 'manage_approvals':
        include 'pages/admin/manage_approvals.php';
        break;
        
    case 'site_settings':
        include 'pages/admin/site_settings.php';
        break;
        
    default:
        include 'pages/404.php';
        break;
} 