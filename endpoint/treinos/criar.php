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

// helper para upload de imagens webp
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
$dados['numeroAula'] = $_POST['numeroAula'] ?? null;
$dados['tipo'] = $_POST['tipo'] ?? null;
$dados['diaSemana'] = $_POST['diaSemana'] ?? null;
$dados['horario'] = $_POST['horario'] ?? null;
$dados['data'] = $_POST['data'] ?? null;
$dados['observacoes'] = $_POST['observacoes'] ?? '';
$dados['isPublico'] = isset($_POST['isPublico']) ? filter_var($_POST['isPublico'], FILTER_VALIDATE_BOOLEAN) : false;

// validação dos campos obrigatórios
if (empty($dados['numeroAula']) || !is_numeric($dados['numeroAula'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O número da aula é obrigatório e deve ser numérico.']);
    exit;
}

if (empty($dados['tipo'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O tipo de treino é obrigatório.']);
    exit;
}

if (empty($dados['diaSemana'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O dia da semana é obrigatório.']);
    exit;
}

if (empty($dados['horario'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'O horário é obrigatório.']);
    exit;
}

if (empty($dados['data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'A data é obrigatória.']);
    exit;
}

// array para armazenar os nomes das imagens processadas
$imagens = [];

// processa as imagens, se houver
if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
    // define o diretório para salvar as imagens
    $targetDir = __DIR__ . '/../../admin/assets/imagens/arquivos/treinos/';

    // verifica se o diretório existe, senão cria
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // processa cada imagem enviada
    $files = $_FILES['imagens'];
    $fileCount = count($files['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] === UPLOAD_ERR_OK) {
            $tmpName = $files['tmp_name'][$i];
            $originalName = $files['name'][$i];
            
            // gera um nome único para o arquivo
            $newFileName = uniqid('treino_') . '_' . time() . '.webp';
            $targetFile = $targetDir . $newFileName;
            
            // converte e salva a imagem em formato WebP
            $result = convertImageToWebP($tmpName, $targetFile);
            
            if ($result) {
                $imagens[] = $newFileName;
            }
        }
    }
}

// usa o repositório para criar o treino com as imagens
use Repositories\TreinoRepository;
$resultado = TreinoRepository::criar($dados, $user->id, $imagens);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;
