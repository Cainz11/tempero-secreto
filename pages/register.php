<?php
// Se já estiver logado, redirecionar para a página inicial
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Processar o formulário de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Criar username a partir do nome
    $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
    
    // Validações
    $errors = [];
    
    // Validação do nome
    if (empty($name)) {
        $errors[] = 'O nome é obrigatório.';
    } elseif (strlen($name) < 3) {
        $errors[] = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (strlen($name) > 100) {
        $errors[] = 'O nome não pode ter mais que 100 caracteres.';
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $name)) {
        $errors[] = 'O nome deve conter apenas letras e espaços.';
    }
    
    // Validação do email
    if (empty($email)) {
        $errors[] = 'O email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email inválido.';
    } elseif (strlen($email) > 100) {
        $errors[] = 'O email não pode ter mais que 100 caracteres.';
    }
    
    // Validação da senha
    if (empty($password)) {
        $errors[] = 'A senha é obrigatória.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
    } elseif (strlen($password) > 50) {
        $errors[] = 'A senha não pode ter mais que 50 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'A senha deve conter pelo menos uma letra maiúscula.';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'A senha deve conter pelo menos uma letra minúscula.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'A senha deve conter pelo menos um número.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'As senhas não conferem.';
    }
    
    // Validação do username gerado
    if (strlen($username) < 3) {
        $errors[] = 'O nome fornecido não gera um nome de usuário válido (mínimo 3 caracteres).';
    }
    
    // Verificar se o email ou username já existem
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        $errors[] = 'Este email ou nome de usuário já está cadastrado.';
    }
    
    if (empty($errors)) {
        // Criar usuário
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, full_name, is_admin, created_at, updated_at) 
            VALUES (?, ?, ?, ?, 0, NOW(), NOW())
        ");
        
        if ($stmt->execute([$username, $email, $hashed_password, $name])) {
            setMessage('success', 'Cadastro realizado com sucesso! Faça login para continuar.');
            redirect(SITE_URL . '?route=login');
        } else {
            setMessage('danger', 'Erro ao criar conta. Tente novamente.');
        }
    } else {
        foreach ($errors as $error) {
            setMessage('danger', $error);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        .register-container {
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
        .form-label {
            font-weight: 600;
            color: #444;
        }
        .password-strength {
            height: 5px;
            border-radius: 2.5px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
        .input-group-text {
            background: transparent;
            border: 2px solid #e1e1e1;
            border-left: none;
        }
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8f9fa;
            color: #666;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        .social-links a:hover {
            transform: translateY(-3px);
            color: #764ba2;
        }
        .animate-up {
            animation: fadeInUp 0.6s ease-out;
        }
        .animate-down {
            animation: fadeInDown 0.6s ease-out;
        }
    </style>
</head>
<body class="bg-light">
    <div class="register-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card animate-up">
                        <div class="card-header text-white">
                            <h1 class="h3 mb-3 animate-down"><?php echo SITE_NAME; ?></h1>
                            <p class="text-white-50 mb-0">Crie sua conta e comece a compartilhar suas receitas</p>
                        </div>
                        <div class="card-body p-4">
                            <?php displayMessages(); ?>

                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome completo</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
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
                                    <div class="password-strength"></div>
                                    <small class="form-text text-muted">A senha deve ter pelo menos 6 caracteres.</small>
                                </div>
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirmar senha</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <span class="input-group-text password-toggle" style="cursor: pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-user-plus me-2"></i> Registrar
                                    </button>
                                </div>
                                <div class="text-center">
                                    <p class="mb-0">Já tem uma conta? 
                                        <a href="<?php echo SITE_URL; ?>?route=login" class="text-primary fw-bold">Entrar</a>
                                    </p>
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
        // Password strength indicator
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthIndicator = document.querySelector('.password-strength');
        const form = document.querySelector('form');

        function validateName(input) {
            const name = input.value.trim();
            const nameRegex = /^[a-zA-ZÀ-ÿ\s]+$/;
            
            if (name.length < 3) {
                input.setCustomValidity('O nome deve ter pelo menos 3 caracteres.');
            } else if (name.length > 100) {
                input.setCustomValidity('O nome não pode ter mais que 100 caracteres.');
            } else if (!nameRegex.test(name)) {
                input.setCustomValidity('O nome deve conter apenas letras e espaços.');
            } else {
                input.setCustomValidity('');
            }
        }

        function validateEmail(input) {
            const email = input.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                input.setCustomValidity('Por favor, insira um email válido.');
            } else if (email.length > 100) {
                input.setCustomValidity('O email não pode ter mais que 100 caracteres.');
            } else {
                input.setCustomValidity('');
            }
        }

        function validatePassword(input) {
            const password = input.value;
            let strength = 0;
            let message = [];
            
            if (password.length < 6) {
                message.push('mínimo 6 caracteres');
            } else {
                strength += 25;
            }
            
            if (!password.match(/[A-Z]/)) {
                message.push('uma letra maiúscula');
            } else {
                strength += 25;
            }
            
            if (!password.match(/[a-z]/)) {
                message.push('uma letra minúscula');
            } else {
                strength += 25;
            }
            
            if (!password.match(/[0-9]/)) {
                message.push('um número');
            } else {
                strength += 25;
            }

            strengthIndicator.style.width = strength + '%';
            
            if (strength <= 25) {
                strengthIndicator.style.backgroundColor = '#dc3545';
            } else if (strength <= 50) {
                strengthIndicator.style.backgroundColor = '#ffc107';
            } else if (strength <= 75) {
                strengthIndicator.style.backgroundColor = '#0dcaf0';
            } else {
                strengthIndicator.style.backgroundColor = '#198754';
            }

            if (message.length > 0) {
                input.setCustomValidity('A senha deve conter: ' + message.join(', '));
            } else {
                input.setCustomValidity('');
            }
        }

        function validateConfirmPassword(input) {
            const password = passwordInput.value;
            const confirmPassword = input.value;
            
            if (password !== confirmPassword) {
                input.setCustomValidity('As senhas não conferem.');
            } else {
                input.setCustomValidity('');
            }
        }

        // Adicionar validações em tempo real
        document.getElementById('name').addEventListener('input', function() {
            validateName(this);
            this.reportValidity();
        });

        document.getElementById('email').addEventListener('input', function() {
            validateEmail(this);
            this.reportValidity();
        });

        passwordInput.addEventListener('input', function() {
            validatePassword(this);
            this.reportValidity();
            if (confirmPasswordInput.value) {
                validateConfirmPassword(confirmPasswordInput);
                confirmPasswordInput.reportValidity();
            }
        });

        confirmPasswordInput.addEventListener('input', function() {
            validateConfirmPassword(this);
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
                    case 'name':
                        validateName(input);
                        break;
                    case 'email':
                        validateEmail(input);
                        break;
                    case 'password':
                        validatePassword(input);
                        break;
                    case 'confirm_password':
                        validateConfirmPassword(input);
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