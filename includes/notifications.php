<?php
// Função para adicionar uma notificação para um usuário
function addNotification($user_id, $message, $type = 'info', $related_user_id = null, $recipe_id = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, message, type, related_user_id, recipe_id, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    return $stmt->execute([$user_id, $message, $type, $related_user_id, $recipe_id]);
}

// Função para marcar notificação como lida
function markNotificationAsRead($notification_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET read_at = NOW() 
        WHERE id = ? AND user_id = ?
    ");
    
    return $stmt->execute([$notification_id, $_SESSION['user_id']]);
}

// Função para buscar notificações não lidas de um usuário
function getUnreadNotifications($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT n.*, 
               r.title as recipe_title,
               u.username as related_username,
               u.full_name as related_full_name
        FROM notifications n
        LEFT JOIN recipes r ON n.recipe_id = r.id
        LEFT JOIN users u ON n.related_user_id = u.id
        WHERE n.user_id = ? AND n.read_at IS NULL 
        ORDER BY n.created_at DESC
    ");
    
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Função para registrar estatísticas diárias
function updateDailyStats($type) {
    global $pdo;
    
    $today = date('Y-m-d');
    
    // Tentar inserir ou atualizar as estatísticas do dia
    try {
        $stmt = $pdo->prepare("
            INSERT INTO daily_stats (date, {$type}_count)
            VALUES (?, 1)
            ON DUPLICATE KEY UPDATE {$type}_count = {$type}_count + 1
        ");
        $stmt->execute([$today]);
    } catch (PDOException $e) {
        // Ignora erro se a tabela ainda não existir
    }
}

// Função para obter estatísticas dos últimos dias
function getDailyStats($days = 7) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT *
            FROM daily_stats
            WHERE date >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
            ORDER BY date DESC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Função para obter estatísticas totais
function getTotalStats() {
    global $pdo;
    
    $stats = [
        'total_users' => 0,
        'total_recipes' => 0,
        'total_likes' => 0,
        'total_comments' => 0,
        'total_views' => 0
    ];
    
    try {
        // Total de usuários
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Total de receitas
        $stmt = $pdo->query("SELECT COUNT(*) FROM recipes WHERE status = 'approved'");
        $stats['total_recipes'] = $stmt->fetchColumn();
        
        // Total de likes
        $stmt = $pdo->query("SELECT COUNT(*) FROM likes");
        $stats['total_likes'] = $stmt->fetchColumn();
        
        // Total de comentários
        $stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
        $stats['total_comments'] = $stmt->fetchColumn();
        
        // Total de visualizações
        $stmt = $pdo->query("SELECT COUNT(*) FROM views");
        $stats['total_views'] = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Ignora erros se alguma tabela não existir
    }
    
    return $stats;
} 