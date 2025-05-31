<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '?route=login');
}

// Marcar todas as notificações como lidas
if (isset($_POST['mark_all_read'])) {
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET read_at = NOW() 
        WHERE user_id = ? AND read_at IS NULL
    ");
    $stmt->execute([$_SESSION['user_id']]);
    redirect(SITE_URL . '?route=notifications');
}

// Marcar uma notificação específica como lida
if (isset($_POST['mark_read']) && isset($_POST['notification_id'])) {
    markNotificationAsRead($_POST['notification_id']);
    redirect(SITE_URL . '?route=notifications');
}

// Buscar todas as notificações do usuário
$stmt = $pdo->prepare("
    SELECT n.*, 
           r.title as recipe_title,
           u.username as related_username,
           u.full_name as related_full_name
    FROM notifications n
    LEFT JOIN recipes r ON n.recipe_id = r.id
    LEFT JOIN users u ON n.related_user_id = u.id
    WHERE n.user_id = ?
    ORDER BY n.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll();

// Separar notificações lidas e não lidas
$unread = array_filter($notifications, function($n) { return is_null($n['read_at']); });
$read = array_filter($notifications, function($n) { return !is_null($n['read_at']); });
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Minhas Notificações</h2>
        <?php if (!empty($unread)): ?>
            <form method="POST" class="d-inline">
                <button type="submit" name="mark_all_read" class="btn btn-secondary">
                    <i class="fas fa-check-double"></i> Marcar todas como lidas
                </button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div class="alert alert-info">
            Você ainda não tem notificações.
        </div>
    <?php else: ?>
        <!-- Notificações não lidas -->
        <?php if (!empty($unread)): ?>
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Não Lidas</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($unread as $notification): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <?php if ($notification['type'] == 'like'): ?>
                                        <div class="me-3 text-danger">
                                            <i class="fas fa-heart fa-lg"></i>
                                        </div>
                                    <?php elseif ($notification['type'] == 'comment'): ?>
                                        <div class="me-3 text-primary">
                                            <i class="fas fa-comment fa-lg"></i>
                                        </div>
                                    <?php elseif ($notification['type'] == 'success'): ?>
                                        <div class="me-3 text-success">
                                            <i class="fas fa-check-circle fa-lg"></i>
                                        </div>
                                    <?php elseif ($notification['type'] == 'warning'): ?>
                                        <div class="me-3 text-warning">
                                            <i class="fas fa-exclamation-circle fa-lg"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                </div>
                                <form method="POST" class="ms-3">
                                    <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                                    <button type="submit" name="mark_read" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Notificações lidas -->
        <?php if (!empty($read)): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lidas</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($read as $notification): ?>
                        <div class="list-group-item text-muted">
                            <div class="d-flex align-items-center">
                                <?php if ($notification['type'] == 'like'): ?>
                                    <div class="me-3">
                                        <i class="fas fa-heart fa-lg"></i>
                                    </div>
                                <?php elseif ($notification['type'] == 'comment'): ?>
                                    <div class="me-3">
                                        <i class="fas fa-comment fa-lg"></i>
                                    </div>
                                <?php elseif ($notification['type'] == 'success'): ?>
                                    <div class="me-3">
                                        <i class="fas fa-check-circle fa-lg"></i>
                                    </div>
                                <?php elseif ($notification['type'] == 'warning'): ?>
                                    <div class="me-3">
                                        <i class="fas fa-exclamation-circle fa-lg"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small>
                                        <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div> 