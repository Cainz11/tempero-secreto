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

// Se for admin, carregar estatísticas do sistema
if (isAdmin()) {
    $total_stats = getAdminDashboardStats();
    $daily_stats = getAdminDailyStats(7); // últimos 7 dias
    $pending = getPendingItems();
    
    // Preparar dados para os gráficos
    $dates = [];
    $new_users = [];
    $new_recipes = [];
    $likes = [];
    $comments = [];
    
    foreach ($daily_stats as $stat) {
        $dates[] = date('d/m', strtotime($stat['date']));
        $new_users[] = $stat['new_users_count'] ?? 0;
        $new_recipes[] = $stat['new_recipes_count'] ?? 0;
        $likes[] = $stat['likes_count'] ?? 0;
        $comments[] = $stat['comments_count'] ?? 0;
    }
}

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

<style>
.admin-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 15px;
    background: linear-gradient(145deg, #ffffff, #f5f5f5);
    box-shadow: 5px 5px 15px rgba(0,0,0,0.1);
}
.admin-card:hover {
    transform: translateY(-5px);
    box-shadow: 8px 8px 20px rgba(0,0,0,0.15);
}
.chart-container {
    position: relative;
    margin: auto;
    height: 300px;
}
.stat-icon {
    padding: 15px;
    border-radius: 50%;
    margin-bottom: 15px;
}
</style>

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
                    <?php if (isAdmin()): ?>
                        <span class="badge bg-danger">Administrador</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Minhas Estatísticas</h5>
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

        <!-- Conteúdo Principal -->
        <div class="col-md-8">
            <?php if (isAdmin()): ?>
                <!-- Dashboard Administrativo -->
                <div class="admin-dashboard mb-4">
                    <h4 class="mb-4">Dashboard Administrativo</h4>
                    
                    <!-- Alertas -->
                    <?php if ($pending['pending_recipes'] > 0 || $pending['pending_comments'] > 0): ?>
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-circle"></i> Itens Pendentes</h5>
                            <?php if ($pending['pending_recipes'] > 0): ?>
                                <p class="mb-1"><?php echo $pending['pending_recipes']; ?> receita(s) aguardando aprovação</p>
                            <?php endif; ?>
                            <?php if ($pending['pending_comments'] > 0): ?>
                                <p class="mb-1"><?php echo $pending['pending_comments']; ?> comentário(s) aguardando aprovação</p>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>?route=manage_approvals" class="btn btn-warning btn-sm mt-2">
                                Gerenciar Aprovações
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Cards de Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon bg-primary bg-opacity-10">
                                        <i class="fas fa-users fa-2x text-primary"></i>
                                    </div>
                                    <h3><?php echo number_format($total_stats['total_users']); ?></h3>
                                    <small class="text-muted">Usuários</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon bg-success bg-opacity-10">
                                        <i class="fas fa-book fa-2x text-success"></i>
                                    </div>
                                    <h3><?php echo number_format($total_stats['total_recipes']); ?></h3>
                                    <small class="text-muted">Receitas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon bg-danger bg-opacity-10">
                                        <i class="fas fa-heart fa-2x text-danger"></i>
                                    </div>
                                    <h3><?php echo number_format($total_stats['total_likes']); ?></h3>
                                    <small class="text-muted">Curtidas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card admin-card text-center">
                                <div class="card-body">
                                    <div class="stat-icon bg-info bg-opacity-10">
                                        <i class="fas fa-comments fa-2x text-info"></i>
                                    </div>
                                    <h3><?php echo number_format($total_stats['total_comments']); ?></h3>
                                    <small class="text-muted">Comentários</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de Atividades -->
                    <div class="card admin-card mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="card-title mb-0">Atividades dos Últimos 7 Dias</h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="activitiesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Minhas Receitas -->
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
                                               class="btn btn-warning btn-sm">Editar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (isAdmin()): ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuração dos gráficos
const dates = <?php echo json_encode($dates); ?>;
const newUsers = <?php echo json_encode($new_users); ?>;
const newRecipes = <?php echo json_encode($new_recipes); ?>;
const likes = <?php echo json_encode($likes); ?>;
const comments = <?php echo json_encode($comments); ?>;

// Gráfico de Atividades
new Chart(document.getElementById('activitiesChart'), {
    type: 'bar',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Novos Usuários',
                data: newUsers,
                backgroundColor: 'rgba(13, 110, 253, 0.7)'
            },
            {
                label: 'Novas Receitas',
                data: newRecipes,
                backgroundColor: 'rgba(25, 135, 84, 0.7)'
            },
            {
                label: 'Curtidas',
                data: likes,
                backgroundColor: 'rgba(220, 53, 69, 0.7)'
            },
            {
                label: 'Comentários',
                data: comments,
                backgroundColor: 'rgba(13, 202, 240, 0.7)'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
<?php endif; ?> 