<?php
require_once 'config/config.php';

$username = 'admin';
$email = 'admin@tempero-secreto.com.br';
$password = 'admin123';
$full_name = 'Administrador';
$is_admin = 1;

// Gerar hash da senha
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Remover usuário admin existente
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    // Criar novo usuário admin
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, is_admin) VALUES (?, ?, ?, ?, ?)");
    $result = $stmt->execute([$username, $email, $hashed_password, $full_name, $is_admin]);

    if ($result) {
        echo "Usuário admin criado com sucesso!\n";
        echo "Email: " . $email . "\n";
        echo "Senha: " . $password . "\n";
        echo "Hash gerado: " . $hashed_password . "\n";
    } else {
        echo "Erro ao criar usuário admin.\n";
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?> 