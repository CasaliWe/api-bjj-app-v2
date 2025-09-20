<?php

// Verifica IPs permitidos e configura CORS
include_once __DIR__ . '/../../helpers/ips-permitidos.php';

// Conn DB
include_once __DIR__ . '/../../config/db.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use Repositories\UserPerfilRepository;
use Core\Logger;

// Verifica se é uma requisição GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método não permitido'
    ]);
    exit;
}

// Verifica se foi informado o bjj_id como parâmetro
if (!isset($_GET['bjj_id']) || empty($_GET['bjj_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'ID do usuário não informado'
    ]);
    exit;
}

// Obtém o bjj_id da requisição
$bjjId = $_GET['bjj_id'];

try {
    // Busca o perfil do usuário pelo bjj_id
    $perfil = UserPerfilRepository::getProfile($bjjId);
    
    // Retorna o perfil do usuário
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Perfil obtido com sucesso',
        'data' => [
            'profile' => $perfil
        ]
    ]);
} catch (\Throwable $th) {
    Logger::log('Erro ao obter perfil do usuário: ' . $th->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao obter perfil do usuário'
    ]);
}