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

// valida o token
use Repositories\UserRepository;
$user = UserRepository::checkToken($token);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}

// lê o corpo da requisição
$input = file_get_contents("php://input");

// decodifica o JSON recebido
$data = json_decode($input, true);

// atualiza os objetivos do usuário
$res = UserRepository::updateObjetivos($user['id'], $data);
if(!$res) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar os objetivos do perfil.']);
    exit;
}

// retorna sucesso
http_response_code(200);
echo json_encode($res);
exit;