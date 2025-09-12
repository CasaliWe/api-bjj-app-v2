<?php

// Verifica IPs permitidos e configura CORS
include_once __DIR__ . '/../../helpers/ips-permitidos.php';

// Conn DB
include_once __DIR__ . '/../../config/db.php';

// processa as variáveis 
require __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// verifica se o método da requisição é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido. Use POST.', 'errorCode' => 405]);
    exit;
}

// pegar o token bearer do cabeçalho Authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Cabeçalho Authorization não encontrado.', 'errorCode' => 401]);
    exit;
}
$authHeader = $headers['Authorization'];
if (strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Cabeçalho Authorization inválido.', 'errorCode' => 401]);
    exit;
}
$token = substr($authHeader, 7);    
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Token não fornecido.', 'errorCode' => 401]);
    exit;
}

// verifica se o token é válido e obtém o usuário
use Repositories\UserRepository;
$user = UserRepository::checkToken($token);
if (!$user) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Token inválido.', 'errorCode' => 401]);
    exit;
}

// recebe os dados JSON do corpo da requisição
$json = file_get_contents('php://input');
$dados = json_decode($json, true);

// verifica se o ID da competição e da imagem foram fornecidos
if (!isset($dados['competicaoId']) || !is_numeric($dados['competicaoId'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID da competição não fornecido ou inválido.', 'errorCode' => 400]);
    exit;
}

if (!isset($dados['imagemId']) || !is_numeric($dados['imagemId'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID da imagem não fornecido ou inválido.', 'errorCode' => 400]);
    exit;
}

// usa o repositório para remover a imagem da competição
use Repositories\CompeticoesRepository;
$resultado = CompeticoesRepository::removerImagem($dados['competicaoId'], $user->id, $dados['imagemId']);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;