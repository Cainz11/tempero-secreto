<?php
// Verificar se o usuário é admin
if (!isAdmin()) {
    redirect(SITE_URL . '?route=home');
}

// Processar ações de aprovação/rejeição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'] ?? '';
    $item_type = $_POST['item_type'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($item_id && $item_type && ($action === 'approve' || $action === 'reject')) {
        if ($item_type === 'recipe') {
            // Buscar informações da receita antes de atualizar
            $stmt = $pdo->prepare("
                SELECT r.title, r.user_id, c.name as category_name, u.full_name as author_name
                FROM recipes r 
                JOIN categories c ON r.category_id = c.id
                JOIN users u ON r.user_id = u.id
                WHERE r.id = ?
            ");
            $stmt->execute([$item_id]);
            $recipe_info = $stmt->fetch();

            // Atualizar status
            $stmt = $pdo->prepare("UPDATE recipes SET status = ? WHERE id = ?");
            $stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $item_id]);

            if ($action === 'approve') {
                $message = 'A receita "' . $recipe_info['title'] . '" foi aprovada e adicionada à categoria ' . $recipe_info['category_name'] . '.';
                setMessage('success', $message);
                
                // Notificar o autor
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO notifications (user_id, message, type, created_at)
                        VALUES (?, ?, 'success', NOW())
                    ");
                    $stmt->execute([
                        $recipe_info['user_id'],
                        'Sua receita "' . $recipe_info['title'] . '" foi aprovada e está disponível na categoria ' . $recipe_info['category_name'] . '!'
                    ]);
                } catch (PDOException $e) {
                    // Ignora erro se a tabela ainda não existir
                }
            } else {
                $message = 'A receita "' . $recipe_info['title'] . '" foi rejeitada.';
                setMessage('warning', $message);
                
                // Notificar o autor
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO notifications (user_id, message, type, created_at)
                        VALUES (?, ?, 'warning', NOW())
                    ");
                    $stmt->execute([
                        $recipe_info['user_id'],
                        'Sua receita "' . $recipe_info['title'] . '" não foi aprovada. Por favor, revise o conteúdo e tente novamente.'
                    ]);
                } catch (PDOException $e) {
                    // Ignora erro se a tabela ainda não existir
                }
            }
        } else {
            // Atualizar status do comentário
            $stmt = $pdo->prepare("UPDATE comments SET status = ? WHERE id = ?");
            $stmt->execute([$action === 'approve' ? 'approved' : 'rejected', $item_id]);
            
            setMessage('success', 'Comentário ' . ($action === 'approve' ? 'aprovado' : 'rejeitado') . ' com sucesso!');
        }
        
        redirect(SITE_URL . '?route=manage_approvals');
    }
}

// Buscar receitas pendentes
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as author_name, c.name as category_name 
    FROM recipes r 
    JOIN users u ON r.user_id = u.id 
    JOIN categories c ON r.category_id = c.id 
    WHERE r.status = 'pending' 
    ORDER BY r.created_at DESC
");
$stmt->execute();
$pending_recipes = $stmt->fetchAll();

// Buscar comentários pendentes
$stmt = $pdo->prepare("
    SELECT c.*, u.full_name as author_name, r.title as recipe_title 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    JOIN recipes r ON c.recipe_id = r.id 
    WHERE c.status = 'pending' 
    ORDER BY c.created_at DESC
");
$stmt->execute();
$pending_comments = $stmt->fetchAll();
?>

<div class="container mt-4">
    <h2>Gerenciar Aprovações</h2>
    
    <!-- Receitas Pendentes -->
    <div class="card mb-4">
        <div class="card-header">
            <h3>Receitas Pendentes</h3>
        </div>
        <div class="card-body">
            <?php if (empty($pending_recipes)): ?>
                <p class="text-muted">Não há receitas pendentes de aprovação.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Categoria</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_recipes as $recipe): ?>
                                <tr>
                                    <td>
                                        <a href="<?= SITE_URL ?>?route=view_recipe&id=<?= $recipe['id'] ?>" target="_blank">
                                            <?= htmlspecialchars($recipe['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($recipe['author_name']) ?></td>
                                    <td><?= htmlspecialchars($recipe['category_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($recipe['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_type" value="recipe">
                                            <input type="hidden" name="item_id" value="<?= $recipe['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                                Aprovar
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                                Rejeitar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Comentários Pendentes -->
    <div class="card">
        <div class="card-header">
            <h3>Comentários Pendentes</h3>
        </div>
        <div class="card-body">
            <?php if (empty($pending_comments)): ?>
                <p class="text-muted">Não há comentários pendentes de aprovação.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Comentário</th>
                                <th>Autor</th>
                                <th>Receita</th>
                                <th>Avaliação</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_comments as $comment): ?>
                                <tr>
                                    <td><?= htmlspecialchars($comment['comment']) ?></td>
                                    <td><?= htmlspecialchars($comment['author_name']) ?></td>
                                    <td>
                                        <a href="<?= SITE_URL ?>?route=view_recipe&id=<?= $comment['recipe_id'] ?>" target="_blank">
                                            <?= htmlspecialchars($comment['recipe_title']) ?>
                                        </a>
                                    </td>
                                    <td><?= str_repeat('⭐', $comment['rating']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="item_type" value="comment">
                                            <input type="hidden" name="item_id" value="<?= $comment['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">
                                                Aprovar
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                                Rejeitar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 