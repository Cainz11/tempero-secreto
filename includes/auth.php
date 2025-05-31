<?php
// Verificar se o usuário está logado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Verificar se o usuário é admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Fazer login
function login($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'] == 1;
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        return false;
    }
}

// Fazer logout
function logout() {
    session_destroy();
    session_start();
}

// Registrar novo usuário
function register($data) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, password, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        
        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

// Verificar se o email já está em uso
function emailExists($email) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
} 