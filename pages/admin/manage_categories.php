<?php
// Verificar se o usuário está logado e é admin
if (!isLoggedIn() || !isAdmin()) {
    setMessage('danger', 'Acesso negado. Você precisa ser um administrador para acessar esta página.');
    redirect(SITE_URL);
}

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $icon = sanitize($_POST['icon']);
            
            if (empty($name)) {
                setMessage('danger', 'O nome da categoria é obrigatório.');
                break;
            }
            
            try {
                $stmt = $pdo->prepare("INSERT INTO categories (name, description, icon) VALUES (?, ?, ?)");
                if ($stmt->execute([$name, $description, $icon])) {
                    setMessage('success', 'Categoria criada com sucesso!');
                } else {
                    setMessage('danger', 'Erro ao criar categoria.');
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Código de erro para duplicidade
                    setMessage('danger', 'Já existe uma categoria com este nome.');
                } else {
                    setMessage('danger', 'Erro ao criar categoria: ' . $e->getMessage());
                }
            }
            break;
            
        case 'update':
            $id = (int)$_POST['id'];
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            $icon = sanitize($_POST['icon']);
            
            if (empty($name)) {
                setMessage('danger', 'O nome da categoria é obrigatório.');
                break;
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, icon = ? WHERE id = ?");
                if ($stmt->execute([$name, $description, $icon, $id])) {
                    setMessage('success', 'Categoria atualizada com sucesso!');
                } else {
                    setMessage('danger', 'Erro ao atualizar categoria.');
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    setMessage('danger', 'Já existe uma categoria com este nome.');
                } else {
                    setMessage('danger', 'Erro ao atualizar categoria: ' . $e->getMessage());
                }
            }
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            
            try {
                // Verificar se existem receitas nesta categoria
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM recipes WHERE category_id = ?");
                $stmt->execute([$id]);
                $recipeCount = $stmt->fetchColumn();
                
                if ($recipeCount > 0) {
                    setMessage('danger', 'Não é possível excluir esta categoria pois existem receitas vinculadas a ela.');
                    break;
                }
                
                $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                if ($stmt->execute([$id])) {
                    setMessage('success', 'Categoria excluída com sucesso!');
                } else {
                    setMessage('danger', 'Erro ao excluir categoria.');
                }
            } catch (PDOException $e) {
                setMessage('danger', 'Erro ao excluir categoria: ' . $e->getMessage());
            }
            break;
    }
}

// Buscar todas as categorias
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    setMessage('danger', 'Erro ao carregar categorias: ' . $e->getMessage());
    $categories = [];
}

// Lista de ícones disponíveis
$available_icons = [
    'fas fa-utensils' => 'Talheres',
    'fas fa-pizza-slice' => 'Pizza',
    'fas fa-ice-cream' => 'Sorvete',
    'fas fa-drumstick-bite' => 'Coxa de Frango',
    'fas fa-leaf' => 'Folha',
    'fas fa-glass-martini-alt' => 'Bebida',
    'fas fa-cookie' => 'Cookie',
    'fas fa-cheese' => 'Queijo',
    'fas fa-candy-cane' => 'Doce',
    'fas fa-fish' => 'Peixe',
    'fas fa-pepper-hot' => 'Pimenta',
    'fas fa-bowl-rice' => 'Tigela de Arroz',
    'fas fa-bread-slice' => 'Pão',
    'fas fa-egg' => 'Ovo',
    'fas fa-hamburger' => 'Hambúrguer',
    'fas fa-coffee' => 'Café',
    'fas fa-wine-glass-alt' => 'Vinho'
];
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gerenciar Categorias</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="fas fa-plus me-2"></i>Nova Categoria
        </button>
    </div>

    <?php displayMessages(); ?>

    <!-- Lista de Categorias -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($categories as $category): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="<?php echo htmlspecialchars($category['icon'] ?? 'fas fa-utensils'); ?> fa-2x me-3"></i>
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($category['name']); ?></h5>
                        </div>
                        <p class="card-text text-muted">
                            <?php echo htmlspecialchars($category['description'] ?? 'Sem descrição.'); ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-primary" 
                                    onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                <i class="fas fa-edit me-2"></i>Editar
                            </button>
                            <button type="button" class="btn btn-outline-danger" 
                                    onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                                <i class="fas fa-trash-alt me-2"></i>Excluir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal de Criação -->
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="create">
                
                <div class="modal-header">
                    <h5 class="modal-title">Nova Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="icon" class="form-label">Ícone</label>
                        <select class="form-select" id="icon" name="icon">
                            <?php foreach ($available_icons as $icon => $label): ?>
                                <option value="<?php echo $icon; ?>">
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Editar Categoria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_icon" class="form-label">Ícone</label>
                        <select class="form-select" id="edit_icon" name="icon">
                            <?php foreach ($available_icons as $icon => $label): ?>
                                <option value="<?php echo $icon; ?>">
                                    <?php echo $label; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a categoria <strong id="delete_name"></strong>?</p>
                    <p class="text-danger mb-0">Esta ação não pode ser desfeita.</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Excluir Categoria</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.btn-group {
    gap: 0.5rem;
}

.modal-content {
    border: none;
    border-radius: 15px;
}

.modal-header {
    border-bottom: none;
    padding-bottom: 0;
}

.modal-footer {
    border-top: none;
    padding-top: 0;
}

.form-control,
.form-select {
    border-radius: 10px;
    padding: 0.75rem 1rem;
    border: 2px solid #e1e1e1;
    transition: all 0.3s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(244,208,63,0.25);
}

.btn-primary {
    background: var(--black);
    border-color: var(--black);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--black);
    transform: translateY(-2px);
}

.btn-outline-primary {
    color: var(--black);
    border-color: var(--black);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--black);
}

.btn-outline-danger {
    color: #dc3545;
    border-color: #dc3545;
}

.btn-outline-danger:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}
</style>

<script>
function editCategory(category) {
    document.getElementById('edit_id').value = category.id;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_description').value = category.description || '';
    document.getElementById('edit_icon').value = category.icon || 'fas fa-utensils';
    
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}

function deleteCategory(id, name) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_name').textContent = name;
    
    new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
}

// Preview dos ícones nos selects
document.addEventListener('DOMContentLoaded', function() {
    const iconSelects = document.querySelectorAll('select[name="icon"]');
    
    iconSelects.forEach(select => {
        // Adicionar ícones às opções
        select.querySelectorAll('option').forEach(option => {
            option.innerHTML = `<i class="${option.value}"></i> ${option.textContent}`;
        });
        
        // Usar biblioteca select2 para melhor visualização
        $(select).select2({
            templateResult: formatIconOption,
            templateSelection: formatIconOption,
            escapeMarkup: function(m) { return m; }
        });
    });
});

function formatIconOption(icon) {
    if (!icon.id) return icon.text;
    return `<i class="${icon.id}"></i> ${icon.text}`;
}
</script> 