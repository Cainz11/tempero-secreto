<?php
// Verificar se o usuário está logado
if (!isLoggedIn()) {
    redirect(SITE_URL . '?route=login');
}

// Obter o ID da receita da URL
$recipe_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$recipe_id) {
    setMessage('danger', 'Receita não encontrada.');
    redirect(SITE_URL . '?route=my_recipes');
}

// Buscar a receita do banco de dados
$stmt = $pdo->prepare("
    SELECT * FROM recipes 
    WHERE id = ?
");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    setMessage('danger', 'Receita não encontrada.');
    redirect(SITE_URL . '?route=my_recipes');
}

// Verificar se o usuário é o autor da receita
if ($recipe['user_id'] !== getCurrentUserId()) {
    setMessage('danger', 'Você não tem permissão para editar esta receita.');
    redirect(SITE_URL . '?route=view_recipe&id=' . $recipe_id);
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $ingredients = sanitize($_POST['ingredients']);
    $instructions = sanitize($_POST['instructions']);
    $preparation_time = (int)$_POST['preparation_time'];
    $servings = (int)$_POST['servings'];
    $difficulty = sanitize($_POST['difficulty']);
    
    // Validações
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'O título é obrigatório.';
    } elseif (strlen($title) > 100) {
        $errors[] = 'O título não pode ter mais que 100 caracteres.';
    }
    
    if (empty($description)) {
        $errors[] = 'A descrição é obrigatória.';
    }
    
    if (empty($ingredients)) {
        $errors[] = 'Os ingredientes são obrigatórios.';
    }
    
    if (empty($instructions)) {
        $errors[] = 'O modo de preparo é obrigatório.';
    }
    
    if ($preparation_time <= 0) {
        $errors[] = 'O tempo de preparo deve ser maior que zero.';
    }
    
    if ($servings <= 0) {
        $errors[] = 'O número de porções deve ser maior que zero.';
    }
    
    if (!in_array($difficulty, ['fácil', 'médio', 'difícil'])) {
        $errors[] = 'Nível de dificuldade inválido.';
    }
    
    // Se não houver erros, atualizar a receita
    if (empty($errors)) {
        // Processar a imagem se uma nova foi enviada
        $image_path = $recipe['image'];
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $upload_result = uploadImage($_FILES['image'], 'recipes');
            if ($upload_result['success']) {
                // Deletar a imagem antiga se existir
                if ($image_path && file_exists($image_path)) {
                    unlink($image_path);
                }
                $image_path = $upload_result['path'];
            } else {
                $errors[] = $upload_result['error'];
            }
        }
        
        if (empty($errors)) {
            $stmt = $pdo->prepare("
                UPDATE recipes 
                SET title = ?, description = ?, ingredients = ?, instructions = ?,
                    preparation_time = ?, servings = ?, difficulty = ?, image = ?,
                    updated_at = NOW()
                WHERE id = ? AND user_id = ?
            ");
            
            if ($stmt->execute([
                $title, $description, $ingredients, $instructions,
                $preparation_time, $servings, $difficulty, $image_path,
                $recipe_id, getCurrentUserId()
            ])) {
                setMessage('success', 'Receita atualizada com sucesso!');
                redirect(SITE_URL . '?route=view_recipe&id=' . $recipe_id);
            } else {
                setMessage('danger', 'Erro ao atualizar a receita. Tente novamente.');
            }
        }
    }
    
    // Se houver erros, exibir as mensagens
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setMessage('danger', $error);
        }
    }
}
?>

<!-- Estilos específicos da página -->
<style>
    .recipe-form {
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        padding: 2rem;
        margin: 2rem 0;
    }
    .recipe-preview {
        max-width: 300px;
        margin: 1rem 0;
        border-radius: 10px;
        overflow: hidden;
    }
    .form-control {
        border-radius: 8px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 1.5rem;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="recipe-form">
                <h1 class="text-center mb-4">Editar Receita</h1>
                
                <?php displayMessages(); ?>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="title" class="form-label">Título da Receita</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($recipe['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  required><?php echo htmlspecialchars($recipe['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ingredients" class="form-label">Ingredientes</label>
                        <textarea class="form-control" id="ingredients" name="ingredients" rows="5" 
                                  required><?php echo htmlspecialchars($recipe['ingredients']); ?></textarea>
                        <small class="text-muted">Digite cada ingrediente em uma nova linha</small>
                    </div>

                    <div class="mb-3">
                        <label for="instructions" class="form-label">Modo de Preparo</label>
                        <textarea class="form-control" id="instructions" name="instructions" rows="5" 
                                  required><?php echo htmlspecialchars($recipe['instructions']); ?></textarea>
                        <small class="text-muted">Digite cada passo em uma nova linha</small>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="preparation_time" class="form-label">Tempo de Preparo (minutos)</label>
                            <input type="number" class="form-control" id="preparation_time" name="preparation_time" 
                                   value="<?php echo $recipe['preparation_time']; ?>" min="1" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="servings" class="form-label">Porções</label>
                            <input type="number" class="form-control" id="servings" name="servings" 
                                   value="<?php echo $recipe['servings']; ?>" min="1" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="difficulty" class="form-label">Dificuldade</label>
                            <select class="form-control" id="difficulty" name="difficulty" required>
                                <option value="fácil" <?php echo $recipe['difficulty'] === 'fácil' ? 'selected' : ''; ?>>Fácil</option>
                                <option value="médio" <?php echo $recipe['difficulty'] === 'médio' ? 'selected' : ''; ?>>Médio</option>
                                <option value="difícil" <?php echo $recipe['difficulty'] === 'difícil' ? 'selected' : ''; ?>>Difícil</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label">Imagem da Receita</label>
                        <?php if ($recipe['image']): ?>
                            <div class="recipe-preview mb-2">
                                <img src="<?php echo SITE_URL . '/' . $recipe['image']; ?>" 
                                     class="img-fluid" alt="Imagem atual da receita">
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Deixe em branco para manter a imagem atual</small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Salvar Alterações
                        </button>
                        <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe_id; ?>" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts específicos da página -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.querySelector('.needs-validation');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });

    // Preview da imagem
    const imageInput = document.getElementById('image');
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.querySelector('.recipe-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'recipe-preview mb-2';
                    imageInput.parentElement.insertBefore(preview, imageInput.nextSibling);
                }
                preview.innerHTML = `<img src="${e.target.result}" class="img-fluid" alt="Preview da nova imagem">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // Formatação dos campos de texto
    const ingredientsTextarea = document.getElementById('ingredients');
    const instructionsTextarea = document.getElementById('instructions');

    function formatTextarea(textarea) {
        let text = textarea.value;
        // Remove linhas vazias extras
        text = text.replace(/\n{3,}/g, '\n\n');
        // Garante que cada item comece em uma nova linha
        text = text.replace(/([^\n])\n([^\n])/g, '$1\n$2');
        textarea.value = text.trim();
    }

    ingredientsTextarea.addEventListener('blur', function() {
        formatTextarea(this);
    });

    instructionsTextarea.addEventListener('blur', function() {
        formatTextarea(this);
    });
});
</script> 