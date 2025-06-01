<?php
require_once 'config/config.php';

try {
    // Verificar se a tabela likes existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'likes'");
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "A tabela 'likes' não existe. Criando...\n";
        
        // Criar a tabela
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS likes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                recipe_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_like (user_id, recipe_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        
        echo "Tabela 'likes' criada com sucesso!\n";
    } else {
        echo "A tabela 'likes' já existe.\n";
        
        // Verificar estrutura da tabela
        $stmt = $pdo->query("DESCRIBE likes");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nEstrutura da tabela:\n";
        foreach ($columns as $column) {
            echo "{$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Key']}\n";
        }
        
        // Verificar chaves estrangeiras
        $stmt = $pdo->query("
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'likes' AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nChaves estrangeiras:\n";
        foreach ($foreignKeys as $fk) {
            echo "{$fk['CONSTRAINT_NAME']} - {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
        }
    }
    
    // Verificar alguns dados
    $stmt = $pdo->query("SELECT COUNT(*) FROM likes");
    $count = $stmt->fetchColumn();
    echo "\nTotal de curtidas: $count\n";
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?> 