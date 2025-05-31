<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        setMessage('danger', 'Por favor, preencha todos os campos.');
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login bem sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                setMessage('success', 'Login realizado com sucesso!');
                
                // Redirecionar para o painel admin se for administrador
                if ($user['is_admin']) {
                    header("Location: " . SITE_URL . "/?route=admin");
                } else {
                    header("Location: " . SITE_URL);
                }
                exit();
            } else {
                // Login falhou
                setMessage('danger', 'Email ou senha inválidos.');
            }
        } catch (PDOException $e) {
            setMessage('danger', 'Erro ao realizar login. Por favor, tente novamente.');
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Login</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($email ?? '') ?>"
                               required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Senha</label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               required>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Entrar</button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <p>Não tem uma conta? <a href="<?php echo SITE_URL; ?>?route=register">Registre-se</a></p>
                </div>
            </div>
        </div>
    </div>
</div> 