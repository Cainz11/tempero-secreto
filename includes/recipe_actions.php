<?php
// Função para dar like em uma receita
function likeRecipe($recipe_id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    try {
        // Verificar se já deu like
        $stmt = $pdo->prepare("
            SELECT id FROM likes 
            WHERE recipe_id = ? AND user_id = ?
        ");
        $stmt->execute([$recipe_id, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            return false; // Já deu like
        }
        
        // Iniciar transação
        $pdo->beginTransaction();
        
        // Inserir like
        $stmt = $pdo->prepare("
            INSERT INTO likes (recipe_id, user_id, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$recipe_id, $_SESSION['user_id']]);
        
        // Buscar informações da receita e do usuário que deu like
        $stmt = $pdo->prepare("
            SELECT r.title, r.user_id, u.full_name as liker_name
            FROM recipes r
            JOIN users u ON u.id = ?
            WHERE r.id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $recipe_id]);
        $info = $stmt->fetch();
        
        // Adicionar notificação para o autor da receita
        if ($info['user_id'] != $_SESSION['user_id']) { // Não notificar se der like na própria receita
            addNotification(
                $info['user_id'],
                $info['liker_name'] . ' curtiu sua receita "' . $info['title'] . '"!',
                'like',
                $_SESSION['user_id'],
                $recipe_id
            );
        }
        
        // Atualizar estatísticas
        updateDailyStats('likes');
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        return false;
    }
}

// Função para remover like de uma receita
function unlikeRecipe($recipe_id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            DELETE FROM likes 
            WHERE recipe_id = ? AND user_id = ?
        ");
        return $stmt->execute([$recipe_id, $_SESSION['user_id']]);
    } catch (PDOException $e) {
        return false;
    }
}

// Função para verificar se o usuário deu like em uma receita
function hasLiked($recipe_id) {
    global $pdo;
    
    if (!isLoggedIn()) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM likes 
            WHERE recipe_id = ? AND user_id = ?
        ");
        $stmt->execute([$recipe_id, $_SESSION['user_id']]);
        return (bool) $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

// Função para contar likes de uma receita
function getLikesCount($recipe_id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM likes 
            WHERE recipe_id = ?
        ");
        $stmt->execute([$recipe_id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

// Função para registrar visualização de receita
function registerView($recipe_id) {
    global $pdo;
    
    try {
        // Registrar visualização
        $stmt = $pdo->prepare("
            INSERT INTO views (recipe_id, user_id, ip_address, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([
            $recipe_id,
            isLoggedIn() ? $_SESSION['user_id'] : null,
            $_SERVER['REMOTE_ADDR']
        ]);
        
        // Atualizar estatísticas
        updateDailyStats('visits');
        
        return true;
    } catch (PDOException $e) {
        return false;
    }
} 