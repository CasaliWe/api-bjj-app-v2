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

// pegando o valor do corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['nome'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetro "nome" é obrigatório.']);
    exit;
}
$nome = $input['nome'];

// usa o repositório para criar a posição
use Repositories\TecnicasRepository;
$resultado = TecnicasRepository::adicionarPosicao($user->id, $nome);

// retorna o resultado
if(!$resultado['success']) {
    http_response_code(400);
} else {
    http_response_code(200);
}

header('Content-Type: application/json');
echo json_encode($resultado);
exit;