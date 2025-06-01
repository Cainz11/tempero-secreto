<?php
// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir itens por página se não estiver definido
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 12);
}

// Parâmetros de paginação e filtro
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';

try {
    // Verificar conexão com o banco
    if (!isset($pdo)) {
        throw new Exception("Erro: Conexão com o banco de dados não estabelecida.");
    }

    // Debug: Imprimir informações da conexão
    error_log("Conexão com o banco estabelecida: " . get_class($pdo));

    // Buscar categorias para o filtro
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    if (!$stmt) {
        throw new Exception("Erro ao preparar query de categorias: " . print_r($pdo->errorInfo(), true));
    }
    $categories = $stmt->fetchAll();

    // Debug: Imprimir número de categorias encontradas
    error_log("Categorias encontradas: " . count($categories));

    // Adicionar verificação de curtidas do usuário atual
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $liked_recipes = [];
        
        try {
            $stmt = $pdo->prepare("SELECT recipe_id FROM likes WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $liked_recipes = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Erro ao buscar curtidas: " . $e->getMessage());
        }
    }

    // Construir a query base
    $query = "SELECT r.*, u.username, u.full_name as author_name, c.name as category_name, c.icon as category_icon,
              (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count
              FROM recipes r 
              LEFT JOIN categories c ON r.category_id = c.id 
              LEFT JOIN users u ON r.user_id = u.id 
              WHERE r.status = 'approved'";

    $params = [];

    // Adicionar filtros
    if ($category_id) {
        $query .= " AND r.category_id = ?";
        $params[] = $category_id;
        // Debug: Imprimir categoria selecionada
        error_log("Filtrando por categoria ID: " . $category_id);
    }

    if ($search) {
        $query .= " AND (r.title LIKE ? OR r.description LIKE ? OR r.ingredients LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        // Debug: Imprimir termo de busca
        error_log("Termo de busca: " . $search);
    }

    // Debug: Imprimir query completa
    error_log("Query: " . $query);
    error_log("Parâmetros: " . print_r($params, true));

    // Contar total de registros para paginação
    $countQuery = str_replace("SELECT r.*, u.username", "SELECT COUNT(*)", $query);
    $countStmt = $pdo->prepare($countQuery);
    if (!$countStmt) {
        throw new Exception("Erro ao preparar query de contagem: " . print_r($pdo->errorInfo(), true));
    }
    $countStmt->execute($params);
    $total_recipes = $countStmt->fetchColumn();

    // Debug: Imprimir total de receitas
    error_log("Total de receitas encontradas: " . $total_recipes);

    // Calcular total de páginas
    $total_pages = ceil($total_recipes / ITEMS_PER_PAGE);
    $page = max(1, min($page, $total_pages));
    $offset = ($page - 1) * ITEMS_PER_PAGE;

    // Adicionar ordenação
    switch ($sort) {
        case 'oldest':
            $query .= " ORDER BY r.created_at ASC";
            break;
        case 'most_liked':
            $query .= " ORDER BY likes_count DESC, r.created_at DESC";
            break;
        case 'alphabetical':
            $query .= " ORDER BY r.title ASC";
            break;
        default: // newest
            $query .= " ORDER BY r.created_at DESC";
    }

    // Adicionar paginação
    $query .= " LIMIT ? OFFSET ?";
    $params[] = ITEMS_PER_PAGE;
    $params[] = $offset;

    // Debug: Imprimir query final
    error_log("Query final: " . $query);
    error_log("Parâmetros finais: " . print_r($params, true));

    // Buscar receitas
    $stmt = $pdo->prepare($query);
    if (!$stmt) {
        throw new Exception("Erro ao preparar query de receitas: " . print_r($pdo->errorInfo(), true));
    }
    $stmt->execute($params);
    $recipes = $stmt->fetchAll();

    // Debug: Imprimir número de receitas retornadas
    error_log("Receitas retornadas: " . count($recipes));

} catch (Exception $e) {
    error_log("Erro no feed de receitas: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    setMessage('danger', 'Ocorreu um erro ao carregar as receitas: ' . $e->getMessage());
    $recipes = [];
    $total_pages = 0;
    $categories = [];
}

// Debug: Imprimir status final
error_log("Status final - Receitas: " . count($recipes) . ", Páginas: " . $total_pages . ", Categorias: " . count($categories));
?>

<div class="container py-4">
    <!-- Título e Descrição -->
    <div class="text-center mb-5">
        <h1 class="display-4">Explorar Receitas</h1>
        <p class="lead text-muted">Descubra receitas deliciosas compartilhadas por nossa comunidade</p>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <input type="hidden" name="route" value="feed">
                
                <!-- Categorias como cards -->
                <div class="col-12 mb-4">
                    <h5 class="mb-3">Categorias</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo SITE_URL; ?>?route=feed" 
                           class="category-pill <?php echo !$category_id ? 'active' : ''; ?>">
                            <i class="fas fa-utensils"></i>
                            <span class="ms-2">Todas</span>
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="<?php echo SITE_URL; ?>?route=feed&category=<?php echo $category['id']; ?><?php 
                                echo $sort ? '&sort=' . urlencode($sort) : '';
                                echo $search ? '&search=' . urlencode($search) : '';
                            ?>" 
                               class="category-pill <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                                <i class="<?php echo htmlspecialchars($category['icon'] ?? 'fas fa-utensils'); ?>"></i>
                                <span class="ms-2"><?php echo htmlspecialchars($category['name']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select shadow-sm" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mais recentes</option>
                        <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Mais antigas</option>
                        <option value="most_liked" <?php echo $sort === 'most_liked' ? 'selected' : ''; ?>>Mais curtidas</option>
                        <option value="alphabetical" <?php echo $sort === 'alphabetical' ? 'selected' : ''; ?>>Ordem alfabética</option>
                    </select>
                </div>
                
                <div class="col-md-9">
                    <div class="input-group shadow-sm">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Buscar por título, descrição ou ingredientes..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                    </div>
                </div>

                <?php if ($category_id): ?>
                    <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Mensagens -->
    <?php displayMessages(); ?>

    <!-- Lista de Receitas -->
    <?php if (empty($recipes)): ?>
        <div class="alert alert-info shadow-sm">
            <i class="fas fa-info-circle me-2"></i>
            <?php if ($search): ?>
                Nenhuma receita encontrada para "<?php echo htmlspecialchars($search); ?>".
            <?php elseif ($category_id): ?>
                Nenhuma receita encontrada nesta categoria.
            <?php else: ?>
                Nenhuma receita encontrada.
            <?php endif; ?>
            <a href="<?php echo SITE_URL; ?>?route=feed" class="alert-link ms-2">Ver todas as receitas</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($recipes as $recipe): ?>
                <div class="col animate-fade-in">
                    <div class="card h-100 shadow-sm recipe-card">
                        <?php if (!empty($recipe['image_url'])): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/recipes/<?php echo htmlspecialchars($recipe['image_url']); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-utensils fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <?php echo htmlspecialchars($recipe['title']); ?>
                            </h5>
                            
                            <div class="recipe-meta mb-3">
                                <small class="text-muted d-block mb-1">
                                    <i class="<?php echo htmlspecialchars($recipe['category_icon'] ?? 'fas fa-utensils'); ?> me-2"></i>
                                    <?php echo htmlspecialchars($recipe['category_name']); ?>
                                </small>
                                <small class="text-muted d-block mb-1">
                                    <i class="fas fa-user me-2"></i>
                                    <?php echo htmlspecialchars($recipe['author_name']); ?>
                                </small>
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar me-2"></i>
                                    <?php echo date('d/m/Y', strtotime($recipe['created_at'])); ?>
                                </small>
                            </div>
                            
                            <p class="card-text">
                                <?php echo nl2br(htmlspecialchars(substr($recipe['description'], 0, 100))); ?>...
                            </p>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                                   class="btn btn-primary btn-lg px-4 rounded-pill">
                                    <i class="fas fa-eye me-2"></i>Ver Receita
                                </a>
                                <div class="d-flex align-items-center">
                                    <button type="button" 
                                            class="btn <?php echo in_array($recipe['id'], $liked_recipes ?? []) ? 'btn-danger' : 'btn-outline-danger'; ?> rounded-pill px-3 like-button" 
                                            data-recipe-id="<?php echo $recipe['id']; ?>"
                                            <?php echo !isLoggedIn() ? 'disabled title="Faça login para curtir"' : ''; ?>>
                                        <i class="fas fa-heart me-2"></i>
                                        <span class="likes-count"><?php echo $recipe['likes_count']; ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginação -->
        <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav aria-label="Navegação das receitas">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo SITE_URL; ?>?route=feed&page=<?php echo $i; ?><?php 
                                echo $category_id ? '&category=' . $category_id : ''; 
                                echo $search ? '&search=' . urlencode($search) : '';
                                echo $sort ? '&sort=' . $sort : '';
                            ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.recipe-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recipe-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.recipe-meta {
    border-left: 3px solid var(--primary-color);
    padding-left: 10px;
}

.category-pill {
    background-color: var(--white);
    color: var(--black);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid var(--gray-200);
    display: inline-flex;
    align-items: center;
}

.category-pill:hover,
.category-pill.active {
    background-color: var(--primary-color);
    color: var(--black);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    text-decoration: none;
}

.category-pill i {
    font-size: 1.1rem;
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

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
    transition: all 0.3s ease;
}

.btn-outline-danger:hover:not([disabled]) {
    background-color: #dc3545;
    color: white;
    transform: translateY(-2px);
}

.btn-outline-danger[disabled] {
    opacity: 0.6;
    cursor: not-allowed;
}

.pagination .page-link {
    color: var(--black);
    border: none;
    margin: 0 2px;
    border-radius: 5px;
    padding: 0.5rem 1rem;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--black);
}

.pagination .page-link:hover {
    background-color: var(--primary-light);
    color: var(--black);
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.like-button {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.like-button:hover:not([disabled]) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220,53,69,0.2);
}

.like-button i {
    transition: all 0.3s ease;
}

.like-button.btn-danger i {
    color: white;
}

.like-button .likes-count {
    font-weight: 600;
    transition: all 0.3s ease;
}

.animate__heartBeat {
    animation-duration: 0.5s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manipular cliques nos botões de curtir
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', async function() {
            if (!this.disabled) {
                const recipeId = this.dataset.recipeId;
                const likesCount = this.querySelector('.likes-count');
                const icon = this.querySelector('i');
                
                // Desabilitar o botão durante a requisição
                this.disabled = true;
                
                try {
                    // Criar FormData para enviar os dados
                    const formData = new FormData();
                    formData.append('recipe_id', recipeId);
                    
                    console.log('Enviando requisição para curtir/descurtir...');
                    
                    // Fazer a requisição AJAX
                    const response = await fetch('<?php echo SITE_URL; ?>/api/like_recipe.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    console.log('Status da resposta:', response.status);
                    
                    // Tentar ler o corpo da resposta como texto primeiro
                    const responseText = await response.text();
                    console.log('Resposta como texto:', responseText);
                    
                    // Tentar fazer o parse do JSON
                    let data;
                    try {
                        data = JSON.parse(responseText);
                        console.log('Dados JSON:', data);
                    } catch (jsonError) {
                        console.error('Erro ao fazer parse do JSON:', jsonError);
                        throw new Error('Resposta inválida do servidor');
                    }
                    
                    // Verificar se a resposta é ok
                    if (!response.ok) {
                        throw new Error(data.error || `Erro do servidor: ${response.status}`);
                    }
                    
                    if (data.success) {
                        console.log('Ação realizada com sucesso:', data.action);
                        
                        // Atualizar o contador com animação
                        likesCount.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            likesCount.textContent = data.likes_count;
                            likesCount.style.transform = 'scale(1)';
                        }, 200);
                        
                        // Atualizar o estilo do botão
                        if (data.action === 'liked') {
                            this.classList.remove('btn-outline-danger');
                            this.classList.add('btn-danger');
                            icon.classList.add('animate__animated', 'animate__heartBeat');
                        } else {
                            this.classList.remove('btn-danger');
                            this.classList.add('btn-outline-danger');
                            icon.classList.add('animate__animated', 'animate__heartBeat');
                        }
                        
                        // Remover classes de animação após a conclusão
                        setTimeout(() => {
                            icon.classList.remove('animate__animated', 'animate__heartBeat');
                        }, 1000);
                    } else {
                        throw new Error(data.error || 'Erro desconhecido ao processar curtida');
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert(`Erro ao processar curtida: ${error.message}`);
                } finally {
                    // Reabilitar o botão
                    this.disabled = false;
                }
            }
        });
    });
});
</script> 