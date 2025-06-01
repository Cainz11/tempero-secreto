<?php
/**
 * Funções específicas para o painel administrativo
 */

/**
 * Retorna estatísticas totais do sistema para o dashboard administrativo
 * @return array
 */
function getAdminDashboardStats() {
    global $pdo;
    
    try {
        // Total de usuários
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $total_users = $stmt->fetchColumn();
        
        // Total de receitas aprovadas
        $stmt = $pdo->query("SELECT COUNT(*) FROM recipes WHERE status = 'approved'");
        $total_recipes = $stmt->fetchColumn();
        
        // Total de likes
        $stmt = $pdo->query("SELECT COUNT(*) FROM recipe_likes");
        $total_likes = $stmt->fetchColumn();
        
        // Total de comentários aprovados
        $stmt = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'");
        $total_comments = $stmt->fetchColumn();
        
        // Total de visualizações
        $stmt = $pdo->query("SELECT COUNT(*) FROM views");
        $total_views = $stmt->fetchColumn();
        
        return [
            'total_users' => $total_users,
            'total_recipes' => $total_recipes,
            'total_likes' => $total_likes,
            'total_comments' => $total_comments,
            'total_views' => $total_views
        ];
    } catch (PDOException $e) {
        // Em caso de erro, retornar zeros
        return [
            'total_users' => 0,
            'total_recipes' => 0,
            'total_likes' => 0,
            'total_comments' => 0,
            'total_views' => 0
        ];
    }
}

/**
 * Retorna estatísticas diárias dos últimos N dias para o dashboard administrativo
 * @param int $days Número de dias para buscar
 * @return array
 */
function getAdminDailyStats($days = 7) {
    global $pdo;
    
    try {
        $stats = [];
        
        // Buscar estatísticas para cada dia
        $query = "
            SELECT 
                DATE(created_at) as date,
                (SELECT COUNT(*) FROM users WHERE DATE(created_at) = DATE(r.created_at)) as new_users_count,
                (SELECT COUNT(*) FROM recipes WHERE DATE(created_at) = DATE(r.created_at) AND status = 'approved') as new_recipes_count,
                (SELECT COUNT(*) FROM recipe_likes WHERE DATE(created_at) = DATE(r.created_at)) as likes_count,
                (SELECT COUNT(*) FROM comments WHERE DATE(created_at) = DATE(r.created_at) AND status = 'approved') as comments_count,
                (SELECT COUNT(*) FROM views WHERE DATE(created_at) = DATE(r.created_at)) as visits_count
            FROM (
                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS created_at
                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
            ) r
            WHERE DATE(created_at) > DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ORDER BY date DESC
            LIMIT ?
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$days, $days]);
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Retorna o número de itens pendentes de aprovação
 * @return array
 */
function getPendingItems() {
    global $pdo;
    
    try {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM recipes WHERE status = 'pending') as pending_recipes,
                (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending_comments
        ";
        $stmt = $pdo->query($query);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return ['pending_recipes' => 0, 'pending_comments' => 0];
    }
}

/**
 * Retorna as últimas atividades do sistema
 * @param int $limit Número de atividades para retornar
 * @return array
 */
function getRecentActivities($limit = 10) {
    global $pdo;
    
    try {
        $query = "
            (SELECT 
                'recipe' as type,
                r.title as title,
                u.full_name as user_name,
                r.created_at as date,
                r.status as status
            FROM recipes r
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
            LIMIT ?)
            
            UNION ALL
            
            (SELECT 
                'comment' as type,
                SUBSTRING(c.content, 1, 100) as title,
                u.full_name as user_name,
                c.created_at as date,
                c.status as status
            FROM comments c
            JOIN users u ON c.user_id = u.id
            ORDER BY c.created_at DESC
            LIMIT ?)
            
            ORDER BY date DESC
            LIMIT ?
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$limit, $limit, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
} 