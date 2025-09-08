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
$token = $data['turnstileToken'] ?? null;
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

// pegando todos os users e verificando se atingiu o limite
use Repositories\UserRepository;
$users = UserRepository::getAll();
if(count($users) >= $_ENV['REGISTER_LIMIT']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Limite máximo de cadastros atingido, tente novamente mais tarde.']);
    exit;
}

// verifica se o email já está cadastrado
if (isset($data['email'])) {
    $existingUser = UserRepository::getByEmail($data['email']);
    if ($existingUser) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Usuário já cadastrado em nossa base de dados.']);
        exit;
    }
}

// faz o cadastro do usuário
$cadastro = UserRepository::create($data);

// verifica se houve erro no cadastro
if (isset($cadastro['error'])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $cadastro['error'], 'details' => $cadastro['details']]);
    exit;
}

// se deu tudo certo retorna a resposta
http_response_code(201);
echo json_encode(['success' => true, 'token' => $cadastro]);