<?php
// Carregar o bootstrap (configurações, funções e sessão)
require_once 'includes/bootstrap.php';

// Roteamento básico
$route = $_GET['route'] ?? 'home';

// Rotas que precisam ser processadas antes do header
$pre_header_routes = ['logout', 'login', 'register'];

// Se for uma rota que precisa ser processada antes do header
if (in_array($route, $pre_header_routes)) {
    include "pages/{$route}.php";
    exit; // Importante para garantir que nada mais seja executado
}

// Verificar redirecionamentos antes de qualquer saída
checkRedirects();

// Incluir o header
include 'includes/header.php';

// Roteamento principal
switch ($route) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'view_recipe':
        include 'pages/view_recipe.php';
        break;
    case 'add_recipe':
        include 'pages/add_recipe.php';
        break;
    case 'edit_recipe':
        include 'pages/edit_recipe.php';
        break;
    case 'feed':
        include 'pages/feed.php';
        break;
    case 'admin':
        include 'pages/admin/dashboard.php';
        break;
    case 'manage_recipes':
        include 'pages/admin/manage_recipes.php';
        break;
    case 'manage_comments':
        include 'pages/admin/manage_comments.php';
        break;
    case 'manage_categories':
        include 'pages/admin/manage_categories.php';
        break;
    case 'manage_approvals':
        include 'pages/admin/manage_approvals.php';
        break;
    case 'manage_users':
        include 'pages/admin/manage_users.php';
        break;
    case 'site_settings':
        include 'pages/admin/site_settings.php';
        break;
    case 'settings':
        include 'pages/settings.php';
        break;
    case 'profile':
        include 'pages/profile.php';
        break;
    case 'my_recipes':
        include 'pages/my_recipes.php';
        break;
    default:
        include 'pages/404.php';
        break;
}

// Incluir o footer
include 'includes/footer.php';