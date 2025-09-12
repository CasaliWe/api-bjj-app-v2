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
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido. Use GET.', 'errorCode' => 405]);
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

// define valores padrão se não foram fornecidos
$filtros = [];
if (isset($_GET['modalidade'])) $filtros['modalidade'] = $_GET['modalidade'];
if (isset($_GET['busca'])) $filtros['busca'] = $_GET['busca'];

$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = isset($_GET['limite']) ? (int)$_GET['limite'] : 10;

// usa o repositório para listar as competições da comunidade
use Repositories\CompeticoesRepository;
$resultado = CompeticoesRepository::listarComunidade($filtros, $pagina, $limite);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;