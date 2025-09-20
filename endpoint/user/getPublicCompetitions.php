<?php

// Verifica IPs permitidos e configura CORS
include_once __DIR__ . '/../../helpers/ips-permitidos.php';

// Conn DB
include_once __DIR__ . '/../../config/db.php';

require_once '../../vendor/autoload.php';

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

// Obtém os parâmetros de paginação (opcionais)
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

try {
    // Busca as competições públicas do usuário
    $resultado = UserPerfilRepository::getPublicCompetitions($bjjId, $pagina, $limite);
    
    // Verifica se a busca foi bem-sucedida
    if (!$resultado['success']) {
        http_response_code(404);
        echo json_encode($resultado);
        exit;
    }
    
    // Retorna as competições públicas
    http_response_code(200);
    echo json_encode($resultado);
} catch (\Throwable $th) {
    Logger::log('Erro ao obter competições públicas: ' . $th->getMessage(), 'ERROR');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao obter competições públicas'
    ]);
}