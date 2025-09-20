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
    echo json_encode(['success' => false, 'message' => 'Método não permitido. Use POST.']);
    exit;
}

// pegar o token bearer do cabeçalho Authorization
$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Cabeçalho Authorization não encontrado.']);
    exit;
}
$authHeader = $headers['Authorization'];
if (strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Cabeçalho Authorization inválido.']);
    exit;
}
$token = substr($authHeader, 7);    
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token não fornecido.']);
    exit;
}

// verifica se o token é válido e obtém o usuário
use Repositories\UserRepository;
$user = UserRepository::checkToken($token);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}

// recebe o corpo da requisição
$input = file_get_contents('php://input');
// decodifica o JSON recebido
$data = json_decode($input, true);

// verifica se o ID da técnica está presente
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O ID da técnica é obrigatório.']);
    exit;
}

// verifica se o status de visibilidade está presente
if (!isset($data['publica'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O status de visibilidade é obrigatório.']);
    exit;
}

// usa o repositório para alterar a visibilidade da técnica
use Repositories\TecnicasRepository;
$resultado = TecnicasRepository::alterarVisibilidade(
    $data['id'], 
    $user->id, 
    (bool)$data['publica']
);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;