    </div> <!-- Fechamento do container principal -->

    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Sobre <?php echo SITE_NAME; ?></h5>
                    <p>Compartilhe e descubra receitas incríveis da culinária brasileira.</p>
                </div>
                <div class="col-md-4">
                    <h5>Links Úteis</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>?route=feed" class="text-light">Receitas</a></li>
                        <li><a href="<?php echo SITE_URL; ?>?route=categories" class="text-light">Categorias</a></li>
                        <?php if (!isLoggedIn()): ?>
                            <li><a href="<?php echo SITE_URL; ?>?route=register" class="text-light">Cadastre-se</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contato</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope"></i> contato@tempero-secreto.com.br</li>
                        <li><i class="fas fa-phone"></i> (11) 99999-9999</li>
                        <li>
                            <a href="#" class="text-light me-2"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-light"><i class="fab fa-youtube"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html> 