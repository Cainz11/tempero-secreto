<?php
// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir o cookie da sessão se existir
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir a sessão
session_destroy();

// Redirecionar para a página inicial com mensagem de sucesso
setMessage('success', 'Você saiu com sucesso! Até logo!');
redirect(SITE_URL); 