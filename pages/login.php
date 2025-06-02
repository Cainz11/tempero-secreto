<?php
// Se já estiver logado, redirecionar para a página inicial
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Processar o formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Validações
    $errors = [];
    
    // Validação do username/email
    if (empty($username)) {
        $errors[] = 'O usuário ou email é obrigatório.';
    } elseif (strlen($username) > 100) {
        $errors[] = 'O usuário ou email não pode ter mais que 100 caracteres.';
    }
    
    // Validação da senha
    if (empty($password)) {
        $errors[] = 'A senha é obrigatória.';
    }
    
    if (empty($errors)) {
        // Validar credenciais
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            
            // Temporariamente removido até a coluna remember_token ser adicionada
            /*if ($remember) {
                // Gerar token único
                $token = bin2hex(random_bytes(32));
                
                // Atualizar o token no banco de dados
                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->execute([$token, $user['id']]);
                
                // Configurar cookie de "lembrar-me" (30 dias)
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }*/
            
            setMessage('success', 'Login realizado com sucesso!');
            redirect(SITE_URL);
        } else {
            setMessage('error', 'Usuário ou senha incorretos.');
            redirect(SITE_URL . '?route=login');
        }
    } else {
        foreach ($errors as $error) {
            setMessage('danger', $error);
        }
    }
}

// Se chegou até aqui, mostrar o formulário de login
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            padding: 2rem 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            border: none;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 2px solid #e1e1e1;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118,75,162,0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(118,75,162,0.4);
        }
        .animate-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-light">
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card animate-up">
                        <div class="card-header text-white">
                            <h1 class="h3 mb-3"><?php echo SITE_NAME; ?></h1>
                            <p class="text-white-50 mb-0">Entre para compartilhar suas receitas</p>
                        </div>
                        <div class="card-body p-4">
                            <?php displayMessages(); ?>

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Usuário ou E-mail</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Senha</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <span class="input-group-text password-toggle" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Lembrar de mim</label>
                                </div>
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-sign-in-alt me-2"></i> Entrar
                                    </button>
                                </div>
                                <div class="text-center">
                                    <p class="mb-0">Não tem uma conta? 
                                        <a href="<?php echo SITE_URL; ?>?route=register" class="text-primary fw-bold">Registre-se</a>
                                    </p>
                                    <a href="<?php echo SITE_URL; ?>" class="text-muted">
                                        <i class="fas fa-home"></i> Voltar para a página inicial
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        function validateUsername(input) {
            const username = input.value.trim();
            
            if (username.length === 0) {
                input.setCustomValidity('O usuário ou email é obrigatório.');
            } else if (username.length > 100) {
                input.setCustomValidity('O usuário ou email não pode ter mais que 100 caracteres.');
            } else {
                input.setCustomValidity('');
            }
        }

        function validatePassword(input) {
            const password = input.value;
            
            if (password.length === 0) {
                input.setCustomValidity('A senha é obrigatória.');
            } else {
                input.setCustomValidity('');
            }
        }

        // Adicionar validações em tempo real
        document.getElementById('username').addEventListener('input', function() {
            validateUsername(this);
            this.reportValidity();
        });

        document.getElementById('password').addEventListener('input', function() {
            validatePassword(this);
            this.reportValidity();
        });

        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Form validation
        form.addEventListener('submit', function(event) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;

            inputs.forEach(function(input) {
                switch(input.id) {
                    case 'username':
                        validateUsername(input);
                        break;
                    case 'password':
                        validatePassword(input);
                        break;
                }

                if (!input.validity.valid) {
                    isValid = false;
                    input.reportValidity();
                }
            });

            if (!isValid) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });

        // Animate form fields on focus
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('animate__animated', 'animate__pulse');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('animate__animated', 'animate__pulse');
            });
        });
    });
    </script>
</body>
</html> 