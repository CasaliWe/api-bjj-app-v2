<?php

include_once __DIR__ . '/../../helpers/ips-permitidos.php';
include_once __DIR__ . '/../../config/db.php';
require __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método não permitido. Use POST.']);
    exit;
}

$headers = getallheaders();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(['message' => 'Cabeçalho Authorization não encontrado.']);
    exit;
}
$authHeader = $headers['Authorization'];
if (strpos($authHeader, 'Bearer ') !== 0) {
    http_response_code(401);
    echo json_encode(['message' => 'Cabeçalho Authorization inválido.']);
    exit;
}
$token = substr($authHeader, 7);
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['message' => 'Token não fornecido.']);
    exit;
}

use Repositories\UserRepository;
$user = UserRepository::checkToken($token);
if (!$user) {
    http_response_code(401);
    echo json_encode(['message' => 'Token inválido.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true) ?? [];

use Repositories\PlanoJogoRepository;
$resultado = PlanoJogoRepository::atualizar($user->id, $data);

header('Content-Type: application/json');
echo json_encode($resultado);
exit;
