<?php
// Verificar se o usuário está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL);
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $user_id = $_POST['user_id'] ?? null;
        
        switch ($_POST['action']) {
            case 'toggle_admin':
                if ($user_id != $_SESSION['user_id']) { // Não permitir que o admin remova seus próprios privilégios
                    $stmt = $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?");
                    $stmt->execute([$user_id]);
                    setMessage('success', 'Status de administrador atualizado com sucesso!');
                }
                break;
                
            case 'toggle_status':
                if ($user_id != $_SESSION['user_id']) { // Não permitir que o admin bloqueie a si mesmo
                    $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'blocked' ELSE 'active' END WHERE id = ?");
                    $stmt->execute([$user_id]);
                    setMessage('success', 'Status do usuário atualizado com sucesso!');
                }
                break;
        }
        redirect(SITE_URL . '?route=manage_users');
    }
}

// Buscar usuários
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$sql = "SELECT * FROM users WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (username LIKE ? OR email LIKE ? OR full_name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if ($status_filter !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Gerenciar Usuários</h2>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="route" value="manage_users">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Buscar por nome, email ou username" 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="status" onchange="this.form.submit()">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Todos os Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Ativos</option>
                        <option value="blocked" <?php echo $status_filter === 'blocked' ? 'selected' : ''; ?>>Bloqueados</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle fa-2x me-2 text-secondary"></i>
                                        <div>
                                            <div><?php echo htmlspecialchars($user['full_name']); ?></div>
                                            <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['status'] === 'active'): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Bloqueado</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-primary">Administrador</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Usuário</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <button type="submit" class="btn btn-sm <?php echo $user['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>" 
                                                    title="<?php echo $user['status'] === 'active' ? 'Bloquear' : 'Desbloquear'; ?>">
                                                <i class="fas <?php echo $user['status'] === 'active' ? 'fa-ban' : 'fa-check'; ?>"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_admin">
                                            <button type="submit" class="btn btn-sm <?php echo $user['is_admin'] ? 'btn-warning' : 'btn-primary'; ?>" 
                                                    title="<?php echo $user['is_admin'] ? 'Remover Admin' : 'Tornar Admin'; ?>">
                                                <i class="fas <?php echo $user['is_admin'] ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 