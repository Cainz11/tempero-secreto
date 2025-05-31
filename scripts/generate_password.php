<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Senha original: " . $password . "\n";
echo "Hash gerado: " . $hash . "\n";
?> 