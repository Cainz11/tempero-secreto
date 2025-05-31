<?php
// Verificar se é admin
if (!isAdmin()) {
    redirect(SITE_URL . '?route=home');
}

// Carregar estatísticas
$total_stats = getTotalStats();
$daily_stats = getDailyStats(7); // últimos 7 dias

// Preparar dados para os gráficos
$dates = [];
$visits = [];
$new_users = [];
$new_recipes = [];
$likes = [];
$comments = [];

foreach ($daily_stats as $stat) {
    $dates[] = date('d/m', strtotime($stat['date']));
    $visits[] = $stat['visits_count'];
    $new_users[] = $stat['new_users_count'];
    $new_recipes[] = $stat['new_recipes_count'];
    $likes[] = $stat['likes_count'];
    $comments[] = $stat['comments_count'];
}
?>

<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard Administrativo</h1>

    <!-- Cards de Estatísticas Totais -->
    <div class="row mb-4">
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                    <h5 class="card-title">Usuários</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_users']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-book fa-2x text-success mb-2"></i>
                    <h5 class="card-title">Receitas</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_recipes']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                    <h5 class="card-title">Likes</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_likes']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-comments fa-2x text-info mb-2"></i>
                    <h5 class="card-title">Comentários</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_comments']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-eye fa-2x text-warning mb-2"></i>
                    <h5 class="card-title">Visualizações</h5>
                    <h3 class="mb-0"><?php echo number_format($total_stats['total_views']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de Visitas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Visitas Diárias</h5>
                </div>
                <div class="card-body">
                    <canvas id="visitsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico de Atividades -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Atividades Diárias</h5>
                </div>
                <div class="card-body">
                    <canvas id="activitiesChart"></canvas>
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
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
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
                borderWidth: 1
            },
            {
                label: 'Novas Receitas',
                data: newRecipes,
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            },
            {
                label: 'Likes',
                data: likes,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            },
            {
                label: 'Comentários',
                data: comments,
                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                borderColor: 'rgb(255, 206, 86)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script> 