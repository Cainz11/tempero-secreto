<?php
// Verificar se é admin
if (!isAdmin()) {
    redirect(SITE_URL . '?route=home');
}

// Carregar estatísticas
$total_stats = getTotalStats();
$daily_stats = getDailyStats(7); // últimos 7 dias

// Buscar itens pendentes
$pending_query = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM recipes WHERE status = 'pending') as pending_recipes,
        (SELECT COUNT(*) FROM comments WHERE status = 'pending') as pending_comments
");
$pending = $pending_query->fetch();

// Preparar dados para os gráficos
$dates = [];
$visits = [];
$new_users = [];
$new_recipes = [];
$likes = [];
$comments = [];

foreach ($daily_stats as $stat) {
    $dates[] = date('d/m', strtotime($stat['date']));
    $visits[] = $stat['visits_count'] ?? 0;
    $new_users[] = $stat['new_users_count'] ?? 0;
    $new_recipes[] = $stat['new_recipes_count'] ?? 0;
    $likes[] = $stat['likes_count'] ?? 0;
    $comments[] = $stat['comments_count'] ?? 0;
}
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
.stat-icon {
    padding: 15px;
    border-radius: 50%;
    margin-bottom: 15px;
}
.chart-container {
    position: relative;
    margin: auto;
    height: 300px;
}
.pending-alert {
    background: linear-gradient(145deg, #ff6b6b, #ff8787);
    color: white;
    border: none;
    border-radius: 15px;
    transition: transform 0.2s;
}
.pending-alert:hover {
    transform: translateY(-3px);
}
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Dashboard Administrativo</h1>
        <?php if ($pending['pending_recipes'] > 0 || $pending['pending_comments'] > 0): ?>
            <a href="<?php echo SITE_URL; ?>?route=manage_approvals" class="btn pending-alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php if ($pending['pending_recipes'] > 0): ?>
                    <?php echo $pending['pending_recipes']; ?> receita(s) pendente(s)
                <?php endif; ?>
                <?php if ($pending['pending_recipes'] > 0 && $pending['pending_comments'] > 0): ?> | <?php endif; ?>
                <?php if ($pending['pending_comments'] > 0): ?>
                    <?php echo $pending['pending_comments']; ?> comentário(s) pendente(s)
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </div>

    <!-- Cards de Estatísticas Totais -->
    <div class="row mb-4">
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card admin-card text-center h-100">
                <div class="card-body">
                    <div class="stat-icon bg-primary bg-opacity-10">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <h5 class="card-title">Usuários</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_users']); ?></h3>
                    <small class="text-muted">Total de usuários</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card admin-card text-center h-100">
                <div class="card-body">
                    <div class="stat-icon bg-success bg-opacity-10">
                        <i class="fas fa-book fa-2x text-success"></i>
                    </div>
                    <h5 class="card-title">Receitas</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_recipes']); ?></h3>
                    <small class="text-muted">Receitas aprovadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card admin-card text-center h-100">
                <div class="card-body">
                    <div class="stat-icon bg-danger bg-opacity-10">
                        <i class="fas fa-heart fa-2x text-danger"></i>
                    </div>
                    <h5 class="card-title">Likes</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_likes']); ?></h3>
                    <small class="text-muted">Total de curtidas</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card admin-card text-center h-100">
                <div class="card-body">
                    <div class="stat-icon bg-info bg-opacity-10">
                        <i class="fas fa-comments fa-2x text-info"></i>
                    </div>
                    <h5 class="card-title">Comentários</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_comments']); ?></h3>
                    <small class="text-muted">Comentários aprovados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card admin-card text-center h-100">
                <div class="card-body">
                    <div class="stat-icon bg-warning bg-opacity-10">
                        <i class="fas fa-eye fa-2x text-warning"></i>
                    </div>
                    <h5 class="card-title">Visualizações</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_views']); ?></h3>
                    <small class="text-muted">Total de views</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Visitas -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Visitas Diárias</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="visitsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Atividades -->
        <div class="col-md-6 mb-4">
            <div class="card admin-card">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">Atividades Diárias</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activitiesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Configuração dos gráficos
const dates = <?php echo json_encode($dates); ?>;
const visits = <?php echo json_encode($visits); ?>;
const newUsers = <?php echo json_encode($new_users); ?>;
const newRecipes = <?php echo json_encode($new_recipes); ?>;
const likes = <?php echo json_encode($likes); ?>;
const comments = <?php echo json_encode($comments); ?>;

// Gráfico de Visitas
new Chart(document.getElementById('visitsChart'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Visitas',
            data: visits,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Gráfico de Atividades
new Chart(document.getElementById('activitiesChart'), {
    type: 'bar',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Novos Usuários',
                data: newUsers,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1,
                borderRadius: 5
            },
            {
                label: 'Novas Receitas',
                data: newRecipes,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1,
                borderRadius: 5
            },
            {
                label: 'Likes',
                data: likes,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1,
                borderRadius: 5
            },
            {
                label: 'Comentários',
                data: comments,
                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                borderColor: 'rgb(255, 206, 86)',
                borderWidth: 1,
                borderRadius: 5
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script> 
</div> 