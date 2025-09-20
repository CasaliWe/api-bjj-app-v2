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
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido. Use POST.', 'errorCode' => 405]);
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

// processa os dados do formulário (multipart/form-data)
$dados = [];
$dados['nomeEvento'] = $_POST['nomeEvento'] ?? null;
$dados['cidade'] = $_POST['cidade'] ?? null;
$dados['data'] = $_POST['data'] ?? null;
$dados['modalidade'] = $_POST['modalidade'] ?? null;
$dados['colocacao'] = $_POST['colocacao'] ?? null;
$dados['categoria'] = $_POST['categoria'] ?? null;
$dados['numeroLutas'] = isset($_POST['numeroLutas']) ? (int)$_POST['numeroLutas'] : 0;
$dados['numeroVitorias'] = isset($_POST['numeroVitorias']) ? (int)$_POST['numeroVitorias'] : 0;
$dados['numeroDerrotas'] = isset($_POST['numeroDerrotas']) ? (int)$_POST['numeroDerrotas'] : 0;
$dados['numeroFinalizacoes'] = isset($_POST['numeroFinalizacoes']) ? (int)$_POST['numeroFinalizacoes'] : 0;
$dados['observacoes'] = $_POST['observacoes'] ?? '';
$dados['isPublico'] = isset($_POST['isPublico']) ? filter_var($_POST['isPublico'], FILTER_VALIDATE_BOOLEAN) : false;

// validação dos campos obrigatórios
if (empty($dados['nomeEvento'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'O nome do evento é obrigatório.', 'errorCode' => 400]);
    exit;
}

// array para armazenar os nomes das imagens processadas
$imagens = [];

// processa as imagens, se houver
if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
    // define o diretório para salvar as imagens
    $targetDir = __DIR__ . '/../../admin/assets/imagens/arquivos/competicoes/';

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
            $newFileName = uniqid('competicao_') . '_' . time() . '.webp';
            $targetFile = $targetDir . $newFileName;
            
            // converte e salva a imagem em formato WebP
            $result = convertImageToWebP($tmpName, $targetFile);
            
            if ($result) {
                $imagens[] = $newFileName;
            }
        }
    }
}

// usa o repositório para criar a competição com as imagens
use Repositories\CompeticoesRepository;
$resultado = CompeticoesRepository::criar($dados, $user->id, $imagens);

// retorna o resultado
header('Content-Type: application/json');
echo json_encode($resultado);
exit;