<?php
// Verificar se o usuário é admin
if (!isAdmin()) {
    redirect(SITE_URL);
}

// Estatísticas gerais
$stats = [];

// Total de usuários
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$stats['total_users'] = $stmt->fetch()['total'];

// Novos usuários nos últimos 7 dias
$stmt = $pdo->query("
    SELECT COUNT(*) as total 
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$stats['new_users_week'] = $stmt->fetch()['total'];

// Total de receitas
$stmt = $pdo->query("SELECT COUNT(*) as total FROM recipes");
$stats['total_recipes'] = $stmt->fetch()['total'];

// Receitas pendentes de aprovação
$stmt = $pdo->query("SELECT COUNT(*) as total FROM recipes WHERE status = 'pending'");
$stats['pending_recipes'] = $stmt->fetch()['total'];

// Total de comentários
$stmt = $pdo->query("SELECT COUNT(*) as total FROM comments");
$stats['total_comments'] = $stmt->fetch()['total'];

// Total de likes
$stmt = $pdo->query("SELECT COUNT(*) as total FROM likes");
$stats['total_likes'] = $stmt->fetch()['total'];

// Receitas mais populares (top 5)
$stmt = $pdo->query("
    SELECT r.title, r.id, COUNT(l.recipe_id) as likes_count
    FROM recipes r
    LEFT JOIN likes l ON r.id = l.recipe_id
    GROUP BY r.id
    ORDER BY likes_count DESC
    LIMIT 5
");
$popular_recipes = $stmt->fetchAll();

// Usuários mais ativos (top 5)
$stmt = $pdo->query("
    SELECT u.username, u.full_name, COUNT(r.id) as recipe_count
    FROM users u
    LEFT JOIN recipes r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY recipe_count DESC
    LIMIT 5
");
$active_users = $stmt->fetchAll();

// Categorias mais usadas
$stmt = $pdo->query("
    SELECT c.name, COUNT(r.id) as recipe_count
    FROM categories c
    LEFT JOIN recipes r ON c.id = r.category_id
    GROUP BY c.id
    ORDER BY recipe_count DESC
");
$categories_stats = $stmt->fetchAll();

// Atividade recente (últimas 10 ações)
$stmt = $pdo->query("
    (SELECT 
        'recipe' as type,
        r.title as title,
        u.username as username,
        r.created_at as date,
        'Nova receita adicionada' as description
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.created_at DESC
    LIMIT 5)
    UNION ALL
    (SELECT 
        'comment' as type,
        r.title as title,
        u.username as username,
        c.created_at as date,
        'Novo comentário adicionado' as description
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN recipes r ON c.recipe_id = r.id
    ORDER BY c.created_at DESC
    LIMIT 5)
    ORDER BY date DESC
    LIMIT 10
");
$recent_activity = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: #FDFAF3; /* Fundo bege bem claro */
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .dashboard-header {
            background: var(--black);
            color: var(--white);
            padding: 2rem 0;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .dashboard-header::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, var(--primary-color));
            opacity: 0.1;
        }

        .dashboard-header h1 {
            position: relative;
            z-index: 1;
            margin: 0;
            padding: 0 1rem;
        }

        .stats-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .card {
            background: #FFFFFF;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: var(--black);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #F4D03F;
        }

        .list-group-item {
            border: none;
            border-radius: 10px !important;
            margin-bottom: 0.5rem;
            background: #FDFAF3;
            transition: all 0.3s ease;
        }

        .list-group-item:hover {
            background: #F4D03F20;
            transform: translateX(5px);
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .container-fluid {
            padding: 0;
        }

        #categoriesChart {
            padding: 1rem;
        }

        .dashboard-content {
            padding: 0 2rem;
        }

        @media (max-width: 768px) {
            .dashboard-content {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <div class="container-fluid">
            <h1>Dashboard</h1>
        </div>
    </div>

    <div class="dashboard-content">
        <!-- Cards de Estatísticas -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stats-card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5>Usuários</h5>
                        <h2><?php echo $stats['total_users']; ?></h2>
                        <p class="mb-0">
                            <i class="fas fa-user-plus"></i>
                            <?php echo $stats['new_users_week']; ?> novos esta semana
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-success text-white h-100">
                    <div class="card-body">
                        <h5>Receitas</h5>
                        <h2><?php echo $stats['total_recipes']; ?></h2>
                        <p class="mb-0">
                            <i class="fas fa-clock"></i>
                            <?php echo $stats['pending_recipes']; ?> pendentes
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-info text-white h-100">
                    <div class="card-body">
                        <h5>Comentários</h5>
                        <h2><?php echo $stats['total_comments']; ?></h2>
                        <p class="mb-0">
                            <i class="fas fa-comments"></i>
                            Em <?php echo $stats['total_recipes']; ?> receitas
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card bg-warning text-white h-100">
                    <div class="card-body">
                        <h5>Likes</h5>
                        <h2><?php echo $stats['total_likes']; ?></h2>
                        <p class="mb-0">
                            <i class="fas fa-heart"></i>
                            Total de curtidas
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Gráfico de Categorias -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Receitas por Categoria</h5>
                        <canvas id="categoriesChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Receitas Populares -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Receitas Mais Populares</h5>
                        <div class="list-group">
                            <?php foreach ($popular_recipes as $recipe): ?>
                            <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $recipe['likes_count']; ?> likes
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Usuários Ativos -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Usuários Mais Ativos</h5>
                        <div class="list-group">
                            <?php foreach ($active_users as $user): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($user['full_name']); ?></h6>
                                    <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    <?php echo $user['recipe_count']; ?> receitas
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Atividade Recente -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Atividade Recente</h5>
                        <div class="list-group">
                            <?php foreach ($recent_activity as $activity): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($activity['description']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('d/m/Y H:i', strtotime($activity['date'])); ?>
                                    </small>
                                </div>
                                <p class="mb-1">
                                    <?php echo htmlspecialchars($activity['title']); ?>
                                    por @<?php echo htmlspecialchars($activity['username']); ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Gráfico de Categorias
    const categoriesData = <?php echo json_encode(array_column($categories_stats, 'recipe_count')); ?>;
    const categoriesLabels = <?php echo json_encode(array_column($categories_stats, 'name')); ?>;
    
    new Chart(document.getElementById('categoriesChart'), {
        type: 'bar',
        data: {
            labels: categoriesLabels,
            datasets: [{
                label: 'Número de Receitas',
                data: categoriesData,
                backgroundColor: 'rgba(244, 208, 63, 0.3)',
                borderColor: '#F4D03F',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            family: 'Poppins'
                        }
                    }
                }
            }
        }
    });

    // Atualização automática a cada 5 minutos
    setInterval(() => {
        window.location.reload();
    }, 300000);
    </script>
</body>
</html> 