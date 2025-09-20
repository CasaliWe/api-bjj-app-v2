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

// helper para upload de vídeos
include_once __DIR__ . '/../../helpers/upload-webp.php';

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

// processa os dados do formulário (multipart/form-data)
$dados = [];
$dados['nome'] = $_POST['nome'] ?? null;
$dados['categoria'] = $_POST['categoria'] ?? null;
$dados['posicao'] = $_POST['posicao'] ?? null;
$dados['passos'] = isset($_POST['passos']) ? json_decode($_POST['passos'], true) : [];
$dados['observacoes'] = isset($_POST['observacoes']) ? json_decode($_POST['observacoes'], true) : [];
$dados['nota'] = isset($_POST['nota']) ? (int)$_POST['nota'] : 0;
$dados['destacado'] = isset($_POST['destacado']) ? filter_var($_POST['destacado'], FILTER_VALIDATE_BOOLEAN) : false;
$dados['publica'] = isset($_POST['publica']) ? filter_var($_POST['publica'], FILTER_VALIDATE_BOOLEAN) : false;
$dados['video'] = $_POST['video'] ?? null;

// validação dos campos obrigatórios
if (empty($dados['nome'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O nome da técnica é obrigatório.']);
    exit;
}

if (empty($dados['categoria'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A categoria é obrigatória.']);
    exit;
}

if (empty($dados['posicao'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A posição é obrigatória.']);
    exit;
}

// verifica se foi enviado um arquivo de vídeo
$videoData = null;
if (isset($_FILES['videoFile']) && $_FILES['videoFile']['error'] === UPLOAD_ERR_OK) {
    $video_width = isset($_POST['video_width']) ? (int)$_POST['video_width'] : null;
    $video_height = isset($_POST['video_height']) ? (int)$_POST['video_height'] : null;
    $videoData = [
        'videoFile' => $_FILES['videoFile'],
        'video_width' => $video_width,
        'video_height' => $video_height
    ];
    // Validar o tamanho e tipo do arquivo de vídeo
    $tipoPermitido = ['video/mp4'];
    $tamanhoMaximo = 20 * 1024 * 1024; // 20MB
    if (!in_array($_FILES['videoFile']['type'], $tipoPermitido)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'O vídeo deve ser do tipo MP4.']);
        exit;
    }
    if ($_FILES['videoFile']['size'] > $tamanhoMaximo) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'O vídeo não pode ser maior que 20MB.']);
        exit;
    }
}

// usa o repositório para criar a técnica
use Repositories\TecnicasRepository;
$resultado = TecnicasRepository::criar($user->id, $dados, $videoData);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;