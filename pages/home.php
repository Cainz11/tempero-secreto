<?php
// Buscar receitas em destaque
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name as author_name, c.name as category_name,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count
    FROM recipes r
    JOIN users u ON r.user_id = u.id
    JOIN categories c ON r.category_id = c.id
    WHERE r.status = 'approved'
    ORDER BY r.created_at DESC
    LIMIT 6
");
$stmt->execute();
$featured_recipes = $stmt->fetchAll();

// Buscar categorias populares
$stmt = $pdo->prepare("
    SELECT c.*, COUNT(r.id) as recipe_count
    FROM categories c
    LEFT JOIN recipes r ON c.id = r.category_id AND r.status = 'approved'
    GROUP BY c.id
    HAVING recipe_count > 0
    ORDER BY recipe_count DESC
    LIMIT 6
");
$stmt->execute();
$popular_categories = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center animate-fade-in">
                <h1>Aprenda a Cozinhar em 5 Minutos!</h1>
                <p class="mb-4">Descubra receitas deliciosas, fáceis e rápidas para seu dia a dia.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?php echo SITE_URL; ?>?route=feed" class="btn btn-primary btn-lg rounded-pill px-4 py-3 shadow-lg" style="position: relative; z-index: 2;" onclick="console.log('Botão clicado'); return true;">
                        <i class="fas fa-search me-2"></i>Explorar Receitas
                    </a>
                    <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>?route=register" class="btn btn-outline-light btn-lg rounded-pill px-4 py-3" style="position: relative; z-index: 2;">
                        <i class="fas fa-user-plus me-2"></i>Criar Conta
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categorias Populares -->
<div class="container mt-5">
    <div class="section-title">
        <h2>Categorias Populares</h2>
        <p>Explore nossas categorias mais acessadas</p>
    </div>
    <div class="row g-4 justify-content-center">
        <?php foreach ($popular_categories as $category): ?>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="<?php echo SITE_URL; ?>?route=feed&category=<?php echo $category['id']; ?>" 
               class="category-pill text-center w-100">
                <?php $icon = !empty($category['icon']) ? $category['icon'] : 'fas fa-utensils'; ?>
                <i class="<?php echo htmlspecialchars($icon); ?>"></i>
                <span class="ms-2"><?php echo htmlspecialchars($category['name']); ?></span>
                <span class="d-block small mt-1"><?php echo $category['recipe_count']; ?> receitas</span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Receitas em Destaque -->
<div class="container mt-5">
    <div class="section-title">
        <h2>Receitas em Destaque</h2>
        <p>As receitas mais recentes e deliciosas da nossa comunidade</p>
    </div>
    <div class="row g-4">
        <?php foreach ($featured_recipes as $recipe): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card recipe-card h-100">
                <?php if (!empty($recipe['image_url'])): ?>
                <img src="<?php echo SITE_URL; ?>/uploads/recipes/<?php echo htmlspecialchars($recipe['image_url']); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                <?php else: ?>
                <img src="<?php echo SITE_URL; ?>/assets/img/recipe-placeholder.jpg" 
                     class="card-img-top" alt="Imagem padrão">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                    <p class="card-text text-muted"><?php echo substr(htmlspecialchars($recipe['description']), 0, 100); ?>...</p>
                    <div class="recipe-meta">
                        <span><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($recipe['author_name']); ?></span>
                        <span><i class="fas fa-heart me-1"></i><?php echo $recipe['likes_count']; ?> curtidas</span>
                    </div>
                    <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                       class="btn btn-primary mt-3 w-100">Ver Receita</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Call to Action -->
<div class="container mt-5">
    <div class="card bg-primary text-white">
        <div class="card-body text-center py-5">
            <h2 class="mb-4">Compartilhe Suas Receitas!</h2>
            <p class="lead mb-4">Faça parte da nossa comunidade e compartilhe suas receitas favoritas com milhares de pessoas.</p>
            <?php if (!isLoggedIn()): ?>
            <a href="<?php echo SITE_URL; ?>?route=register" class="btn btn-light btn-lg">
                Começar Agora
            </a>
            <?php else: ?>
            <a href="<?php echo SITE_URL; ?>?route=add_recipe" class="btn btn-light btn-lg">
                Adicionar Receita
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, var(--black), var(--text-color));
    padding: 6rem 0;
    margin-bottom: 3rem;
    text-align: center;
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.hero-section::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, var(--primary-color));
    opacity: 0.1;
    pointer-events: none;
}

.hero-section h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    position: relative;
    z-index: 1;
}

.hero-section p {
    font-size: 1.5rem;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    position: relative;
    z-index: 1;
}

.hero-section .btn {
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
}

.hero-section .btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: var(--black);
}

.hero-section .btn-primary:hover {
    background-color: var(--white);
    border-color: var(--white);
    color: var(--black);
    transform: translateY(-3px);
}

.hero-section .btn-outline-light:hover {
    background-color: var(--white);
    color: var(--black);
    transform: translateY(-3px);
}

@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 0;
    }

    .hero-section h1 {
        font-size: 2.5rem;
    }

    .hero-section p {
        font-size: 1.2rem;
    }

    .hero-section .d-flex {
        flex-direction: column;
    }

    .hero-section .btn {
        width: 100%;
        margin: 0.5rem 0;
    }
}
</style> 