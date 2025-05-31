<?php
// Buscar receitas mais curtidas
$stmt = $pdo->query("
    SELECT r.*, c.name as category_name, u.full_name as username,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count,
           (SELECT COUNT(*) FROM views WHERE recipe_id = r.id) as views_count
    FROM recipes r
    LEFT JOIN categories c ON r.category_id = c.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.status = 'approved'
    ORDER BY likes_count DESC
    LIMIT 3
");
$most_liked_recipes = $stmt->fetchAll();

// Buscar receitas mais visualizadas
$stmt = $pdo->query("
    SELECT r.*, c.name as category_name, u.full_name as username,
           (SELECT COUNT(*) FROM likes WHERE recipe_id = r.id) as likes_count,
           (SELECT COUNT(*) FROM views WHERE recipe_id = r.id) as views_count
    FROM recipes r
    LEFT JOIN categories c ON r.category_id = c.id
    LEFT JOIN users u ON r.user_id = u.id
    WHERE r.status = 'approved'
    ORDER BY views_count DESC
    LIMIT 3
");
$most_viewed_recipes = $stmt->fetchAll();

// Buscar categorias mais populares
$stmt = $pdo->query("
    SELECT c.*, COUNT(r.id) as recipe_count,
           SUM((SELECT COUNT(*) FROM views WHERE recipe_id = r.id)) as total_views
    FROM categories c
    LEFT JOIN recipes r ON c.id = r.category_id AND r.status = 'approved'
    GROUP BY c.id
    ORDER BY total_views DESC
    LIMIT 6
");
$popular_categories = $stmt->fetchAll();
?>

<!-- Hero Section -->
<div class="bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4">Tempero Secreto</h1>
                <p class="lead">Descubra, compartilhe e saboreie as melhores receitas da comunidade.</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>?route=register" class="btn btn-light btn-lg">
                        Comece a Cozinhar
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>?route=add_recipe" class="btn btn-light btn-lg">
                        Compartilhe sua Receita
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-center">
                <img src="<?php echo SITE_URL; ?>/assets/images/cooking.svg" alt="Cooking Illustration" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Categorias Populares -->
    <section class="mb-5">
        <h2 class="mb-4">Categorias Populares</h2>
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-4">
            <?php foreach ($popular_categories as $category): ?>
                <div class="col">
                    <a href="<?php echo SITE_URL; ?>?route=feed&category=<?php echo $category['id']; ?>" 
                       class="text-decoration-none">
                        <div class="card h-100 text-center">
                            <div class="card-body">
                                <i class="fas fa-utensils fa-2x mb-2 text-primary"></i>
                                <h5 class="card-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?php echo $category['recipe_count']; ?> receitas
                                    </small>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Receitas Mais Curtidas -->
    <section class="mb-5">
        <h2 class="mb-4">Receitas Mais Curtidas</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($most_liked_recipes as $recipe): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($recipe['image_url']): ?>
                            <img src="<?php echo SITE_URL . '/uploads/' . $recipe['image_url']; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text text-muted">
                                <small>
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['username']); ?><br>
                                    <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                    <i class="fas fa-heart text-danger"></i> <?php echo $recipe['likes_count']; ?> curtidas
                                </small>
                            </p>
                            <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                               class="btn btn-primary">Ver Receita</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Receitas Mais Visualizadas -->
    <section class="mb-5">
        <h2 class="mb-4">Receitas Mais Visualizadas</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($most_viewed_recipes as $recipe): ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if ($recipe['image_url']): ?>
                            <img src="<?php echo SITE_URL . '/uploads/' . $recipe['image_url']; ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($recipe['title']); ?>"
                                 style="height: 200px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($recipe['title']); ?></h5>
                            <p class="card-text text-muted">
                                <small>
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($recipe['username']); ?><br>
                                    <i class="fas fa-folder"></i> <?php echo htmlspecialchars($recipe['category_name']); ?><br>
                                    <i class="fas fa-eye"></i> <?php echo $recipe['views_count']; ?> visualizações
                                </small>
                            </p>
                            <a href="<?php echo SITE_URL; ?>?route=view_recipe&id=<?php echo $recipe['id']; ?>" 
                               class="btn btn-primary">Ver Receita</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div> 