<?php

// Rotas públicas
switch ($_GET['route']) {
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
        
    // Rotas administrativas
    case 'admin':
        include 'pages/admin/dashboard.php';
        break;
        
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