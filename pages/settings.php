<?php
if (!isLoggedIn()) {
    redirect(SITE_URL . '?route=login');
}

// Buscar informações do usuário
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Processar atualização de informações pessoais
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);

    $errors = [];

    // Validações
    if (empty($full_name)) {
        $errors[] = "O nome completo é obrigatório.";
    }
    if (empty($email)) {
        $errors[] = "O e-mail é obrigatório.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail inválido.";
    }
    if (empty($username)) {
        $errors[] = "O nome de usuário é obrigatório.";
    }

    // Verificar se email ou username já existem (exceto para o usuário atual)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
    $stmt->execute([$email, $username, $user_id]);
    if ($stmt->rowCount() > 0) {
        $errors[] = "E-mail ou nome de usuário já está em uso.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET full_name = ?, email = ?, phone = ?, username = ?, bio = ?
            WHERE id = ?
        ");
        
        if ($stmt->execute([$full_name, $email, $phone, $username, $bio, $user_id])) {
            $_SESSION['user_name'] = $username;
            setMessage('success', 'Informações atualizadas com sucesso!');
            redirect(SITE_URL . '?route=settings');
        } else {
            $errors[] = "Erro ao atualizar as informações.";
        }
    }
}

// Processar alteração de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];

    // Verificar senha atual
    if (!password_verify($current_password, $user['password'])) {
        $errors[] = "Senha atual incorreta.";
    }

    // Validar nova senha
    if (strlen($new_password) < 6) {
        $errors[] = "A nova senha deve ter pelo menos 6 caracteres.";
    }
    if ($new_password !== $confirm_password) {
        $errors[] = "As senhas não coincidem.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        
        if ($stmt->execute([$hashed_password, $user_id])) {
            setMessage('success', 'Senha alterada com sucesso!');
            redirect(SITE_URL . '?route=settings');
        } else {
            $errors[] = "Erro ao alterar a senha.";
        }
    }
}
?>

<div class="container py-4">
    <h2 class="mb-4">Configurações da Conta</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Informações Pessoais -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Informações Pessoais</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografia</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"
                                      placeholder="Conte um pouco sobre você..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Alterar Senha -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> Alterar Senha</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Senha Atual</label>
                            <input type="password" class="form-control" id="current_password" 
                                   name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Nova Senha</label>
                            <input type="password" class="form-control" id="new_password" 
                                   name="new_password" required>
                            <div class="form-text">A senha deve ter pelo menos 6 caracteres.</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-key"></i> Alterar Senha
                        </button>
                    </form>
                </div>
            </div>

            <?php if (isAdmin()): ?>
            <!-- Configurações Administrativas -->
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Configurações de Administrador</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="<?php echo SITE_URL; ?>?route=manage_categories" class="list-group-item list-group-item-action">
                            <i class="fas fa-folder"></i> Gerenciar Categorias
                        </a>
                        <a href="<?php echo SITE_URL; ?>?route=manage_users" class="list-group-item list-group-item-action">
                            <i class="fas fa-users"></i> Gerenciar Usuários
                        </a>
                        <a href="<?php echo SITE_URL; ?>?route=site_settings" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog"></i> Configurações do Site
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Máscara para o campo de telefone
document.addEventListener('DOMContentLoaded', function() {
    var phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    }
});
</script> 