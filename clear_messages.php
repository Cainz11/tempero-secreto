<?php
require_once 'config.php';
session_start();

// Limpar mensagens flash
unset($_SESSION['messages']);
unset($_SESSION['redirect']);

echo "Mensagens flash limpas com sucesso!\n"; 