<?php
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get existing foreign key constraints
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = '" . DB_NAME . "'
        AND TABLE_NAME = 'notifications'
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    // Drop existing foreign keys
    while ($row = $stmt->fetch()) {
        $pdo->exec("ALTER TABLE notifications DROP FOREIGN KEY " . $row['CONSTRAINT_NAME']);
    }

    // Drop existing columns if they exist
    $pdo->exec("ALTER TABLE notifications 
                DROP COLUMN IF EXISTS recipe_id,
                DROP COLUMN IF EXISTS related_user_id");

    // Add columns
    $pdo->exec("ALTER TABLE notifications 
                ADD COLUMN recipe_id INT NULL,
                ADD COLUMN related_user_id INT NULL");

    // Add foreign keys
    $pdo->exec("ALTER TABLE notifications
                ADD CONSTRAINT fk_notifications_recipe
                FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE SET NULL");

    $pdo->exec("ALTER TABLE notifications
                ADD CONSTRAINT fk_notifications_user
                FOREIGN KEY (related_user_id) REFERENCES users(id) ON DELETE SET NULL");

    echo "Database structure updated successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 