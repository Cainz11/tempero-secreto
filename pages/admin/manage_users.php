<?php
// Verificar se o usuário está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    setMessage('danger', 'Acesso negado. Você precisa ser um administrador para acessar esta página.');
    redirect(SITE_URL);
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    if (!$user_id) {
        setMessage('danger', 'ID do usuário não fornecido.');
        redirect(SITE_URL . '?route=manage_users');
    }
    
    try {
        switch ($action) {
            case 'toggle_status':
                // Verificar status atual do usuário
                $stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Alternar entre 'active' e 'blocked'
                    $new_status = ($user['status'] === 'active') ? 'blocked' : 'active';
                    
                    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
                    $stmt->execute([$new_status, $user_id]);
                    
                    setMessage('success', 'Status do usuário atualizado com sucesso.');
                } else {
                    setMessage('danger', 'Usuário não encontrado.');
                }
                break;
                
            case 'delete':
                // Não permitir deletar o próprio usuário
                if ($user_id === $_SESSION['user_id']) {
                    setMessage('danger', 'Você não pode deletar sua própria conta.');
                    break;
                }
                
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                setMessage('success', 'Usuário deletado com sucesso.');
                break;
                
            case 'toggle_admin':
                // Não permitir remover privilégios do próprio usuário
                if ($user_id === $_SESSION['user_id']) {
                    setMessage('danger', 'Você não pode alterar seus próprios privilégios de administrador.');
                    break;
                }
                
                // Verificar status atual do admin
                $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Alternar status de admin
                    $new_admin_status = !$user['is_admin'];
                    
                    $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
                    $stmt->execute([$new_admin_status, $user_id]);
                    
                    setMessage('success', 'Privilégios de administrador ' . 
                        ($new_admin_status ? 'concedidos' : 'removidos') . ' com sucesso.');
                } else {
                    setMessage('danger', 'Usuário não encontrado.');
                }
                break;
        }
    } catch (PDOException $e) {
        setMessage('danger', 'Erro ao processar ação: ' . $e->getMessage());
    }
    
    redirect(SITE_URL . '?route=manage_users');
}

// Buscar usuários
try {
    // Verificar se existe a coluna status na tabela users
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'status'");
    if (!$stmt->fetch()) {
        // Se não existir, criar a coluna
        $pdo->exec("ALTER TABLE users ADD COLUMN status ENUM('active', 'blocked') NOT NULL DEFAULT 'active'");
    }

    $stmt = $pdo->query("
        SELECT id, username, email, full_name, is_admin, created_at, status,
        (SELECT COUNT(*) FROM recipes WHERE user_id = users.id) as recipe_count
        FROM users
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    setMessage('danger', 'Erro ao buscar usuários: ' . $e->getMessage());
    $users = [];
}
?>

<div class="container py-4">
    <h1 class="mb-4">Gerenciar Usuários</h1>
    
    <?php displayMessages(); ?>
    
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Receitas</th>
                            <th>Status</th>
                            <th>Admin</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['recipe_count']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $user['status'] === 'active' ? 'Ativo' : 'Bloqueado'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user['is_admin'] ? 'primary' : 'secondary'; ?>">
                                        <?php echo $user['is_admin'] ? 'Sim' : 'Não'; ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <form method="POST" class="d-inline me-2">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <button type="submit" class="btn btn-sm btn-<?php echo $user['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                    <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                <i class="fas fa-<?php echo $user['status'] === 'active' ? 'ban' : 'check'; ?>"></i>
                                                <?php echo $user['status'] === 'active' ? 'Bloquear' : 'Ativar'; ?>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline me-2">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_admin">
                                            <button type="submit" class="btn btn-sm btn-primary"
                                                    <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                <i class="fas fa-user-shield"></i>
                                                <?php echo $user['is_admin'] ? 'Remover Admin' : 'Tornar Admin'; ?>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                                <i class="fas fa-trash"></i>
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.btn-group .btn {
    margin-right: 0.25rem;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5em 0.75em;
}
</style> 