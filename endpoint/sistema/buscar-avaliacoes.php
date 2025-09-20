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

// verifica se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido. Use GET.']);
    exit;
}

// usa o repositório para buscar avaliações
use Repositories\SistemaRepository;
$resultado = SistemaRepository::getAllAvaliacoes();

if (empty($resultado)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Nenhuma avaliação encontrada.']);
    exit;
}

// retorna o resultado
header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => $resultado]);
exit;