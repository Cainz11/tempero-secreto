<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '?route=login');
}

// Buscar receitas do usuário
$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? 'all';

$sql = "
    SELECT r.*, c.name as category_name,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count,
           (SELECT COUNT(*) FROM views WHERE recipe_id = r.id) as views_count,
           (SELECT COUNT(*) FROM comments WHERE recipe_id = r.id AND status = 'approved') as comments_count
    FROM recipes r
    LEFT JOIN categories c ON r.category_id = c.id
    WHERE r.user_id = ?
";

$params = [$user_id];

if ($status_filter !== 'all') {
    $sql .= " AND r.status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY r.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();

// Agrupar receitas por status
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book"></i> Minhas Receitas</h2>
        <a href="<?php echo SITE_URL; ?>?route=add_recipe" class="btn btn-success">
            <i class="fas fa-plus"></i> Nova Receita
        </a>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="route" value="my_recipes">
                <div class="col-md-4">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Todas as Receitas</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pendentes</option>
                        <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Aprovadas</option>
                        <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Não Aprovadas</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total de Receitas</h6>
                    <h2 class="mb-0"><?php echo count($recipes); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Pendentes</h6>
                    <h2 class="mb-0"><?php echo count($pending_recipes); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Aprovadas</h6>
                    <h2 class="mb-0"><?php echo count($approved_recipes); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Não Aprovadas</h6>
                    <h2 class="mb-0"><?php echo count($rejected_recipes); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($recipes)): ?>
        <div class="alert alert-info">
            <p class="mb-0">Você ainda não publicou nenhuma receita. 
                <a href="<?php echo SITE_URL; ?>?route=add_recipe" class="alert-link">Clique aqui</a> 
                para adicionar sua primeira receita!</p>
        </div>
    <?php else: ?>
        <!-- Lista de Receitas -->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col">
                    <div class="card h-100 <?php echo getRecipeCardClass($recipe['status']); ?>">
                        <?php if ($recipe['image_url']): ?>
                            <img src="<?php echo SITE_URL . '/uploads/' . htmlspecialchars($recipe['image_url']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                                <?php echo getRecipeStatusBadge($recipe['status']); ?>
                            </h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                    <i class="fas fa-calendar"></i> <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?><br>
                                    <?php if ($recipe['status'] === 'approved'): ?>
                                        <i class="fas fa-heart"></i> <?php echo $recipe['likes_count']; ?> curtidas<br>
                                        <i class="fas fa-eye"></i> <?php echo $recipe['views_count']; ?> visualizações<br>
                                        <i class="fas fa-comment"></i> <?php echo $recipe['comments_count']; ?> comentários
                                    <?php endif; ?>
                                </small>
                            </p>
                            <div class="btn-group">
                                <?php if ($recipe['status'] === 'approved'): ?>
                                    <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                                       class="btn btn-primary btn-sm">Ver</a>
                                <?php endif; ?>
                                <a href="<?php echo SITE_URL; ?>?route=edit_recipe&id=<?php echo $recipe['id']; ?>" 
                                   class="btn btn-warning btn-sm">Editar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
function getRecipeCardClass($status) {
    switch ($status) {
        case 'pending':
            return 'border-warning';
        case 'approved':
            return '';
        case 'rejected':
            return 'border-danger';
        default:
            return '';
    }
}

function getRecipeStatusBadge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Pendente</span>';
        case 'approved':
            return '<span class="badge bg-success">Aprovada</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Não Aprovada</span>';
        default:
            return '';
    }
}
?> 