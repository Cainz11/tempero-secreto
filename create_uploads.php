<?php
// Criar diretório de uploads se não existir
$uploadDir = __DIR__ . '/uploads';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
    echo "Diretório de uploads criado com sucesso!";
} else {
    echo "Diretório de uploads já existe.";
}
?> 