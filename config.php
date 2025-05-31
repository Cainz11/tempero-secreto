<?php
// Configurações do site
define('SITE_NAME', 'Tempero Secreto');
define('SITE_URL', 'http://localhost/tempero-secreto');

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'tempero_secreto');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações de upload
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de paginação
define('ITEMS_PER_PAGE', 12); // Número de itens por página

// Configurações de erro (remova em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Diretórios do sistema
define('ROOT_DIR', __DIR__);
define('INCLUDES_DIR', ROOT_DIR . '/includes');
define('PAGES_DIR', ROOT_DIR . '/pages');
define('ASSETS_DIR', ROOT_DIR . '/assets');

// Criar diretórios necessários se não existirem
$directories = [
    UPLOAD_DIR,
    UPLOAD_DIR . '/recipes',
    UPLOAD_DIR . '/users',
    ASSETS_DIR . '/css',
    ASSETS_DIR . '/js',
    ASSETS_DIR . '/img'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
} 