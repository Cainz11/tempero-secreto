    </div><!-- Fim do .main-container -->
    
    <footer class="footer" role="contentinfo">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="h5 mb-3"><?php echo SITE_NAME; ?></h2>
                    <p class="mb-0">Compartilhe suas receitas favoritas e descubra novos sabores.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <nav class="footer-nav" aria-label="Links úteis">
                        <ul class="list-inline mb-3">
                            <li class="list-inline-item">
                                <a href="<?php echo SITE_URL; ?>?route=about" class="text-decoration-none">Sobre</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?php echo SITE_URL; ?>?route=contact" class="text-decoration-none">Contato</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="<?php echo SITE_URL; ?>?route=accessibility" class="text-decoration-none">Acessibilidade</a>
                            </li>
                        </ul>
                    </nav>
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Botão de Acessibilidade -->
    <div id="accessibility-toolbar" class="accessibility-toolbar" role="region" aria-label="Ferramentas de acessibilidade">
        <button id="increase-text" class="a11y-btn" aria-label="Aumentar texto">
            <i class="fas fa-text-height" aria-hidden="true"></i>
        </button>
        <button id="decrease-text" class="a11y-btn" aria-label="Diminuir texto">
            <i class="fas fa-text-height fa-rotate-180" aria-hidden="true"></i>
        </button>
        <button id="high-contrast" class="a11y-btn" aria-label="Alternar alto contraste">
            <i class="fas fa-adjust" aria-hidden="true"></i>
        </button>
        <button id="dyslexic-font" class="a11y-btn" aria-label="Alternar fonte para dislexia">
            <i class="fas fa-font" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>

    <!-- Script de Acessibilidade -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funções para ajustar tamanho do texto
        let currentFontSize = 100;
        
        document.getElementById('increase-text').addEventListener('click', function() {
            if (currentFontSize < 150) {
                currentFontSize += 10;
                document.body.style.fontSize = currentFontSize + '%';
            }
        });

        document.getElementById('decrease-text').addEventListener('click', function() {
            if (currentFontSize > 70) {
                currentFontSize -= 10;
                document.body.style.fontSize = currentFontSize + '%';
            }
        });

        // Alto contraste
        document.getElementById('high-contrast').addEventListener('click', function() {
            document.body.classList.toggle('high-contrast');
        });

        // Fonte para dislexia
        document.getElementById('dyslexic-font').addEventListener('click', function() {
            document.body.classList.toggle('dyslexic-font');
        });

        // Atalhos de teclado
        document.addEventListener('keydown', function(e) {
            // Alt + 1: Ir para o conteúdo principal
            if (e.altKey && e.key === '1') {
                e.preventDefault();
                document.getElementById('main-content').focus();
            }
            // Alt + 2: Ir para o menu
            if (e.altKey && e.key === '2') {
                e.preventDefault();
                document.querySelector('.navbar').focus();
            }
        });
    });
    </script>

    <style>
    /* Estilos para a barra de acessibilidade */
    .accessibility-toolbar {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #fff;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .a11y-btn {
        background: #4A90E2;
        color: #fff;
        border: none;
        padding: 8px;
        margin: 2px;
        border-radius: 4px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .a11y-btn:hover {
        background: #357ABD;
    }

    .a11y-btn:focus {
        outline: 3px solid #000;
        outline-offset: 2px;
    }

    /* Alto contraste */
    body.high-contrast {
        background: #000 !important;
        color: #fff !important;
    }

    body.high-contrast a {
        color: #ffff00 !important;
    }

    body.high-contrast .card,
    body.high-contrast .navbar {
        background: #333 !important;
        color: #fff !important;
    }

    /* Fonte para dislexia */
    body.dyslexic-font {
        font-family: 'OpenDyslexic', 'Comic Sans MS', cursive !important;
        line-height: 1.8 !important;
        letter-spacing: 0.05em !important;
    }

    /* Melhorias gerais de acessibilidade */
    :focus {
        outline: 3px solid #4A90E2;
        outline-offset: 2px;
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        border: 0;
    }
    </style>
</body>
</html> 