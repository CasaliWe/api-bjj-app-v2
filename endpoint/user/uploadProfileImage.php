<?php

// Verifica IPs permitidos e configura CORS
include_once __DIR__ . '/../../helpers/ips-permitidos.php';

// Conn DB
include_once __DIR__ . '/../../config/db.php';

// importando a func webp convert
include_once __DIR__ . '/../../helpers/upload-webp.php';

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

// pegando o arquivo enviado
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erro no upload da imagem.']);
    exit;
}

// salvando a imagem como webp
$pastaDestino = __DIR__ . '/../../admin/assets/imagens/arquivos/perfil/';
$nomeArquivoWebP = salvarImagemWebP($_FILES['image'], $pastaDestino, 'profile-');
if(!$nomeArquivoWebP) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao processar a imagem.']);
    exit;
}

// atualiza a imagem de perfil no banco
$data = ['imagem' => $nomeArquivoWebP];
$res = UserRepository::update($user['id'], $data);
if(!$res) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar foto de perfil.']);
    exit;
}

// retorna sucesso
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Foto de Perfil atualizada com sucesso.', 'image' => $nomeArquivoWebP]);
exit;