<?php
// Iniciar ou resumir a sessão
session_start();

// Garantir que não haja saída antes dos headers
ob_start();

// Definir header de content-type
header('Content-Type: application/json; charset=utf-8');

// Prevenir cache
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir arquivos necessários
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php'; // Adicionando o arquivo de autenticação

// Função para retornar resposta JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Log para debug
error_log("Requisição recebida em like_recipe.php");

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    sendJsonResponse(['error' => 'Método não permitido'], 405);
}

// Verificar se o usuário está logado
if (!isLoggedIn()) {
    error_log("Usuário não autenticado");
    sendJsonResponse(['error' => 'Usuário não autenticado'], 401);
}

// Obter dados da requisição
$recipe_id = isset($_POST['recipe_id']) ? (int)$_POST['recipe_id'] : 0;
$user_id = $_SESSION['user_id'];

error_log("Dados recebidos - recipe_id: $recipe_id, user_id: $user_id");

if (!$recipe_id) {
    error_log("ID da receita não fornecido");
    sendJsonResponse(['error' => 'ID da receita não fornecido'], 400);
}

try {
    // Verificar se a receita existe
    $stmt = $pdo->prepare("SELECT id FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    if (!$stmt->fetch()) {
        error_log("Receita não encontrada: $recipe_id");
        sendJsonResponse(['error' => 'Receita não encontrada'], 404);
    }

    // Verificar se o usuário já curtiu esta receita
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $existing_like = $stmt->fetch();

    error_log("Verificação de curtida existente: " . ($existing_like ? "Sim" : "Não"));

    if ($existing_like) {
        // Se já curtiu, remove a curtida
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$user_id, $recipe_id]);
        $action = 'unliked';
        error_log("Curtida removida");
    } else {
        // Se não curtiu, adiciona a curtida
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, recipe_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $recipe_id]);
        $action = 'liked';
        error_log("Curtida adicionada");
    }

    // Buscar o novo número de curtidas
    $stmt = $pdo->prepare("SELECT COUNT(*) as likes_count FROM likes WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    $likes_count = $stmt->fetch()['likes_count'];

    error_log("Novo número de curtidas: $likes_count");

    // Retornar resposta
    $response = [
        'success' => true,
        'action' => $action,
        'likes_count' => (int)$likes_count // Garantir que seja número
    ];
    error_log("Resposta: " . json_encode($response));
    sendJsonResponse($response);

} catch (PDOException $e) {
    error_log("Erro no banco de dados: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    error_log("Trace: " . $e->getTraceAsString());
    
    sendJsonResponse([
        'error' => 'Erro ao processar curtida',
        'details' => $e->getMessage()
    ], 500);
}

// Limpar qualquer saída em buffer
ob_end_flush();
?> 