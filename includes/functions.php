<?php
/**
 * Funções auxiliares para o sistema
 */

/**
 * Retorna o ID do usuário atual
 * @return int|null
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Retorna os dados do usuário atual
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

/**
 * Sanitiza uma string para evitar XSS
 * @param string $str
 * @return string
 */
function sanitize($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Redireciona para uma URL
 * @param string $url
 */
function redirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        echo '<script>window.location.href = "' . $url . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $url . '"></noscript>';
        exit;
    }
}

/**
 * Define uma mensagem flash
 * @param string $type success|danger|warning|info
 * @param string $message
 */
function setMessage($type, $message) {
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }
    $_SESSION['messages'][] = [
        'type' => $type,
        'text' => $message
    ];
}

/**
 * Exibe as mensagens flash
 */
function displayMessages() {
    if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) {
            echo '<div class="alert alert-' . $message['type'] . ' alert-dismissible fade show" role="alert">';
            echo $message['text'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
        }
        // Limpar as mensagens após exibi-las
        $_SESSION['messages'] = [];
    }
}

/**
 * Faz upload de uma imagem
 * @param array $file $_FILES['campo']
 * @param string $directory diretório onde a imagem será salva
 * @return array ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function uploadImage($file, $directory) {
    // Verificar se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'error' => 'Erro no upload do arquivo.'
        ];
    }

    // Verificar o tipo do arquivo
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return [
            'success' => false,
            'error' => 'Tipo de arquivo não permitido. Use apenas JPG, PNG ou GIF.'
        ];
    }

    // Verificar o tamanho do arquivo (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return [
            'success' => false,
            'error' => 'O arquivo é muito grande. O tamanho máximo permitido é 5MB.'
        ];
    }

    // Criar o diretório se não existir
    $upload_dir = 'uploads/' . $directory;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Gerar um nome único para o arquivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . '/' . $filename;

    // Mover o arquivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'path' => $filepath
        ];
    }

    return [
        'success' => false,
        'error' => 'Erro ao salvar o arquivo.'
    ];
}

/**
 * Formata uma data para o formato brasileiro
 * @param string $date
 * @return string
 */
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
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