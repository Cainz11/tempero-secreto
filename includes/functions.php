<?php
// Função para sanitizar input
function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Função para redirecionar
function redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo '<script>window.location.href="' . $url . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $url . '" /></noscript>';
        exit;
    }
}

// Função para definir mensagens flash
function setMessage($type, $message) {
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }
    $_SESSION['messages'][] = [
        'type' => $type,
        'text' => $message
    ];
}

// Função para exibir mensagens
function displayMessages() {
    if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) {
            echo '<div class="alert alert-' . $message['type'] . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($message['text']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        unset($_SESSION['messages']);
    }
}

// Função para verificar CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

// Função para formatar data
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Função para limitar texto
function limitText($text, $limit = 100) {
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit) . '...';
}

// Função para gerar slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

// Função para validar upload de imagem
function validateImage($file) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return 'Formato de arquivo não permitido. Use: ' . implode(', ', $allowed);
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        return 'O arquivo é muito grande. Tamanho máximo: 5MB';
    }
    
    return true;
}

// Função para fazer upload de imagem
function uploadImage($file, $path) {
    $validation = validateImage($file);
    if ($validation !== true) {
        return $validation;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid() . '.' . $ext;
    $destination = $path . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return 'Erro ao fazer upload da imagem';
    }
    
    return $filename;
} 