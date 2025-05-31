<?php
// Apenas exibir a página 404, sem redirecionamentos
?>

<div class="text-center py-5">
    <h1 class="display-1">404</h1>
    <h2 class="mb-4">Página não encontrada</h2>
    <p class="lead mb-4">A página que você está procurando não existe ou foi movida.</p>
    
    <?php if (!isLoggedIn()): ?>
        <div class="mb-4">
            <p>Faça login para acessar todas as funcionalidades do site:</p>
            <a href="<?php echo SITE_URL; ?>?route=login" class="btn btn-primary me-2">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </a>
            <a href="<?php echo SITE_URL; ?>?route=register" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Registrar
            </a>
        </div>
    <?php endif; ?>
    
    <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary">
        <i class="fas fa-home"></i> Voltar para a página inicial
    </a>
</div> 