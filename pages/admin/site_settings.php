<?php
// Verificar se o usuário está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    redirect(SITE_URL);
}

// Processar configurações do site
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $site_name = trim($_POST['site_name']);
    $site_description = trim($_POST['site_description']);
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    $recipes_per_page = (int)$_POST['recipes_per_page'];
    $allow_comments = isset($_POST['allow_comments']) ? 1 : 0;
    $require_approval = isset($_POST['require_approval']) ? 1 : 0;
    
    $errors = [];
    
    // Validações
    if (empty($site_name)) {
        $errors[] = "O nome do site é obrigatório.";
    }
    if ($recipes_per_page < 1) {
        $errors[] = "O número de receitas por página deve ser maior que zero.";
    }
    
    if (empty($errors)) {
        // Atualizar configurações na tabela settings
        $settings = [
            'site_name' => $site_name,
            'site_description' => $site_description,
            'maintenance_mode' => $maintenance_mode,
            'recipes_per_page' => $recipes_per_page,
            'allow_comments' => $allow_comments,
            'require_approval' => $require_approval
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$key, $value, $value]);
        }
        
        setMessage('success', 'Configurações atualizadas com sucesso!');
        redirect(SITE_URL . '?route=site_settings');
    }
}

// Buscar configurações atuais
$settings = [];
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="container py-4">
    <h2 class="mb-4"><i class="fas fa-cog"></i> Configurações do Site</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="">
                <!-- Configurações Básicas -->
                <h5 class="border-bottom pb-2 mb-4">Configurações Básicas</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Nome do Site</label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                   value="<?php echo htmlspecialchars($settings['site_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="site_description" class="form-label">Descrição do Site</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                    ><?php echo htmlspecialchars($settings['site_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="recipes_per_page" class="form-label">Receitas por Página</label>
                            <input type="number" class="form-control" id="recipes_per_page" name="recipes_per_page"
                                   value="<?php echo htmlspecialchars($settings['recipes_per_page'] ?? '12'); ?>" min="1">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode"
                                   <?php echo ($settings['maintenance_mode'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="maintenance_mode">
                                Modo de Manutenção
                            </label>
                            <div class="form-text">Quando ativado, apenas administradores podem acessar o site.</div>
                        </div>
                    </div>
                </div>

                <!-- Configurações de Conteúdo -->
                <h5 class="border-bottom pb-2 mb-4">Configurações de Conteúdo</h5>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="allow_comments" name="allow_comments"
                                   <?php echo ($settings['allow_comments'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="allow_comments">
                                Permitir Comentários
                            </label>
                            <div class="form-text">Habilita ou desabilita comentários em todas as receitas.</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="require_approval" name="require_approval"
                                   <?php echo ($settings['require_approval'] ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="require_approval">
                                Exigir Aprovação
                            </label>
                            <div class="form-text">Receitas precisam ser aprovadas por um administrador antes de serem publicadas.</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" name="update_settings" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 