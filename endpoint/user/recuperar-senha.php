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
    echo json_encode(['error' => 'Método não permitido. Use POST.']);
    exit;
}

// lê o corpo da requisição
$input = file_get_contents("php://input");

// decodifica o JSON recebido
$data = json_decode($input, true);

// Verifica a cloudflare key turnstile
$secretKey = $_ENV['CLOUDFLARE_TURNSTILE_SECRET_KEY'];
$token = $data['token'] ?? null;
if (!$token) {
    echo json_encode(['success' => false, 'message' => 'Não foi possível validar o token turnstile.']);
    exit;
}
// Faz a requisição para a API da Cloudflare
$ch = curl_init("https://challenges.cloudflare.com/turnstile/v0/siteverify");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "secret={$secretKey}&response={$token}");
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (empty($result["success"])) {
    echo json_encode(['success' => false, 'message' => 'Você precisa validar o token turnstile.']);
    exit;
}


// verifica se o email já está cadastrado
use Repositories\UserRepository;
$existingUser = UserRepository::getByEmail($data['email']);
if (!$existingUser) {
    echo json_encode(['success' => false, 'message' => 'E-mail não encontrado.']);
    exit;
}

// chama a func q cria a nova senha e envia o email
$res = UserRepository::recuperarSenha($existingUser['id'], $existingUser['email'], $existingUser['nome']);

// se deu tudo certo retorna a resposta
if(!$res['success']) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao enviar o e-mail de recuperação. Tente novamente mais tarde.']);
    exit;
}

http_response_code(201);
echo json_encode(['success' => true, 'message' => 'E-mail de recuperação enviado com sucesso.']);