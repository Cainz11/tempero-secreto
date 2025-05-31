<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '?route=login');
}

// Buscar informações do usuário
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM recipes WHERE user_id = u.id) as total_recipes,
           (SELECT COUNT(*) FROM recipes WHERE user_id = u.id AND status = 'pending') as pending_recipes,
           (SELECT COUNT(*) FROM recipes WHERE user_id = u.id AND status = 'approved') as approved_recipes,
           (SELECT COUNT(*) FROM likes WHERE recipe_id IN (SELECT id FROM recipes WHERE user_id = u.id)) as total_likes_received,
           (SELECT COUNT(*) FROM views WHERE recipe_id IN (SELECT id FROM recipes WHERE user_id = u.id)) as total_views_received,
           (SELECT COUNT(*) FROM likes WHERE user_id = u.id) as total_likes_given,
           (SELECT COUNT(*) FROM views WHERE user_id = u.id) as total_recipes_viewed
    FROM users u 
    WHERE u.id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Buscar receitas do usuário
$stmt = $pdo->prepare("
    SELECT r.*, c.name as category_name,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count,
           (SELECT COUNT(*) FROM views WHERE recipe_id = r.id) as views_count
    FROM recipes r
    LEFT JOIN categories c ON r.category_id = c.id
    WHERE r.user_id = ?
    ORDER BY 
        CASE r.status 
            WHEN 'pending' THEN 1 
            WHEN 'approved' THEN 2 
            ELSE 3 
        END,
        r.created_at DESC
");
$stmt->execute([$user_id]);
$recipes = $stmt->fetchAll();

// Separar receitas por status
$pending_recipes = array_filter($recipes, function($recipe) {
    return $recipe['status'] === 'pending';
});
$approved_recipes = array_filter($recipes, function($recipe) {
    return $recipe['status'] === 'approved';
});
$rejected_recipes = array_filter($recipes, function($recipe) {
    return $recipe['status'] === 'rejected';
});
?>

<div class="container py-4">
    <div class="row">
        <!-- Perfil do Usuário -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle fa-5x mb-3 text-primary"></i>
                    <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <p>Membro desde <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Estatísticas</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Receitas Aprovadas
                            <span class="badge bg-success rounded-pill"><?php echo $user['approved_recipes']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Receitas Pendentes
                            <span class="badge bg-warning rounded-pill"><?php echo $user['pending_recipes']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Curtidas Recebidas
                            <span class="badge bg-primary rounded-pill"><?php echo $user['total_likes_received']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Visualizações Recebidas
                            <span class="badge bg-info rounded-pill"><?php echo $user['total_views_received']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Receitas Curtidas
                            <span class="badge bg-secondary rounded-pill"><?php echo $user['total_likes_given']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Receitas do Usuário -->
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Minhas Receitas</h4>
                <a href="<?php echo SITE_URL; ?>?route=add_recipe" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nova Receita
                </a>
            </div>

            <?php if (!empty($pending_recipes)): ?>
                <div class="alert alert-warning">
                    <h5><i class="fas fa-clock"></i> Receitas Aguardando Aprovação</h5>
                    <p>Suas receitas estão sendo analisadas pelo administrador e em breve estarão disponíveis no site.</p>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                    <?php foreach ($pending_recipes as $recipe): ?>
                        <div class="col">
                            <div class="card h-100 border-warning">
                                <?php if ($recipe['image_url']): ?>
                                    <img src="<?php echo SITE_URL . '/uploads/' . htmlspecialchars($recipe['image_url']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                         style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($recipe['title']); ?>
                                        <span class="badge bg-warning text-dark">Pendente</span>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <small>
                                            <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                            <i class="fas fa-calendar"></i> Enviada em: <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($recipes)): ?>
                <div class="alert alert-info">
                    Você ainda não publicou nenhuma receita.
                </div>
            <?php else: ?>
                <?php if (!empty($approved_recipes)): ?>
                    <h5 class="mb-3"><i class="fas fa-check-circle text-success"></i> Receitas Aprovadas</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">
                        <?php foreach ($approved_recipes as $recipe): ?>
                            <div class="col">
                                <div class="card h-100">
                                    <?php if ($recipe['image_url']): ?>
                                        <img src="<?php echo SITE_URL . '/uploads/' . htmlspecialchars($recipe['image_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                                        <p class="card-text text-muted">
                                            <small>
                                                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                                <i class="fas fa-heart"></i> <?php echo $recipe['likes_count']; ?> curtidas<br>
                                                <i class="fas fa-eye"></i> <?php echo $recipe['views_count']; ?> visualizações
                                            </small>
                                        </p>
                                        <div class="btn-group">
                                            <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                                               class="btn btn-primary btn-sm">Ver</a>
                                            <a href="<?php echo SITE_URL; ?>?route=edit_recipe&id=<?php echo $recipe['id']; ?>" 
                                               class="btn btn-warning btn-sm">Editar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($rejected_recipes)): ?>
                    <h5 class="mb-3"><i class="fas fa-times-circle text-danger"></i> Receitas Não Aprovadas</h5>
                    <div class="row row-cols-1 row-cols-md-2 g-4">
                        <?php foreach ($rejected_recipes as $recipe): ?>
                            <div class="col">
                                <div class="card h-100 border-danger">
                                    <?php if ($recipe['image_url']): ?>
                                        <img src="<?php echo SITE_URL . '/uploads/' . htmlspecialchars($recipe['image_url']); ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                             style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?php echo htmlspecialchars($recipe['title']); ?>
                                            <span class="badge bg-danger">Não Aprovada</span>
                                        </h5>
                                        <p class="card-text text-muted">
                                            <small>
                                                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                                <i class="fas fa-calendar"></i> Enviada em: <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?>
                                            </small>
                                        </p>
                                        <div class="btn-group">
                                            <a href="<?php echo SITE_URL; ?>?route=edit_recipe&id=<?php echo $recipe['id']; ?>" 
                                               class="btn btn-warning btn-sm">Editar e Reenviar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Dashboard Admin (apenas para administradores) -->
            <?php if (isAdmin() && $user_id == $_SESSION['user_id']): ?>
                <div class="card mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-line"></i> Dashboard Administrativo</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Buscar estatísticas administrativas
                        $admin_stats = $pdo->query("
                            SELECT 
                                (SELECT COUNT(*) FROM recipes WHERE status = 'pending') as pending_recipes,
                                (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending_comments,
                                (SELECT COUNT(*) FROM recipes WHERE status = 'rejected') as rejected_recipes,
                                (SELECT COUNT(*) FROM recipes WHERE status = 'approved') as total_recipes,
                                (SELECT COUNT(*) FROM users) as total_users
                        ")->fetch();

                        // Buscar últimas receitas rejeitadas
                        $rejected_recipes_query = $pdo->query("
                            SELECT r.*, u.username, c.name as category_name 
                            FROM recipes r 
                            JOIN users u ON r.user_id = u.id 
                            LEFT JOIN categories c ON r.category_id = c.id 
                            WHERE r.status = 'rejected' 
                            ORDER BY r.updated_at DESC 
                            LIMIT 5
                        ");
                        $recent_rejected = $rejected_recipes_query->fetchAll();
                        ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-warning text-dark h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Receitas Pendentes</h6>
                                        <h2 class="mb-0"><?php echo $admin_stats['pending_recipes']; ?></h2>
                                        <a href="<?php echo SITE_URL; ?>?route=manage_approvals" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-info text-dark h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Comentários Pendentes</h6>
                                        <h2 class="mb-0"><?php echo $admin_stats['pending_comments']; ?></h2>
                                        <a href="<?php echo SITE_URL; ?>?route=manage_approvals" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-danger text-white h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Receitas Rejeitadas</h6>
                                        <h2 class="mb-0"><?php echo $admin_stats['rejected_recipes']; ?></h2>
                                        <a href="<?php echo SITE_URL; ?>?route=manage_approvals" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-success text-white h-100">
                                    <div class="card-body">
                                        <h6 class="card-title">Total de Receitas</h6>
                                        <h2 class="mb-0"><?php echo $admin_stats['total_recipes']; ?></h2>
                                        <a href="<?php echo SITE_URL; ?>?route=admin" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Últimas Receitas Rejeitadas -->
                        <div class="mt-4">
                            <h5 class="border-bottom pb-2">Últimas Receitas Rejeitadas</h5>
                            <?php if (empty($recent_rejected)): ?>
                                <p class="text-muted">Não há receitas rejeitadas recentemente.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Receita</th>
                                                <th>Autor</th>
                                                <th>Categoria</th>
                                                <th>Data</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_rejected as $recipe): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                                                    <td><?php echo htmlspecialchars($recipe['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($recipe['category_name']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($recipe['updated_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                                                               class="btn btn-primary" title="Ver Receita">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="<?php echo SITE_URL; ?>?route=manage_approvals&review=<?php echo $recipe['id']; ?>" 
                                                               class="btn btn-warning" title="Reavaliar">
                                                                <i class="fas fa-sync-alt"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="<?php echo SITE_URL; ?>?route=manage_approvals&filter=rejected" class="btn btn-outline-primary btn-sm">
                                        Ver Todas as Receitas Rejeitadas
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div> 