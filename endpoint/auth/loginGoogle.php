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
$dados = json_decode($input, true);

// pegando o código do Google (authorization code)
$authCode = $dados['token'] ?? ''; // aqui é na real o "code"

if (!$authCode) {
    echo json_encode(['success' => false, 'message' => 'Authorization code não enviado']);
    exit;
}

// Faz a troca do authorization code por tokens no Google
$url = "https://oauth2.googleapis.com/token";
$postData = [
    'code' => $authCode,
    'client_id' => $_ENV['GOOGLE_CLIENT_ID'],
    'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'],
    'redirect_uri' => $_ENV['GOOGLE_REDIRECT_URI'], // deve ser o mesmo que tu configurou no Google Cloud Console
    'grant_type' => 'authorization_code'
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($postData),
    ],
];

$context  = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if (!$response) {
    echo json_encode(['success' => false, 'message' => 'Erro ao trocar authorization code por tokens']);
    exit;
}

$tokens = json_decode($response, true);

// Verifica se recebeu id_token
if (!isset($tokens['id_token'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Google não retornou id_token',
        'google_response' => $tokens
    ]);
    exit;
}

// Valida o id_token (JWT) com o Google
$idToken = $tokens['id_token'];
$verifyUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
$userInfo = json_decode(file_get_contents($verifyUrl), true);

// Monta dados do usuário
$userData = [
    'name'    => $userInfo['name'] ?? '',
    'email'   => $userInfo['email'] ?? '',
    'picture' => $userInfo['picture'] ?? ''
];

// verifica se o email ja existe no banco
use Repositories\UserRepository;
$user = UserRepository::getByEmail($userData['email']);
if($user){
    // fazendo login
    $tokenResLogin = UserRepository::loginGoogle($userData['email']);
    if($tokenResLogin['success']){
        echo json_encode([
            'success' => true,
            'message' => 'Login bem-sucedido',
            'token' => $tokenResLogin['token']
        ]);
    }else{
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao fazer login com Google'
        ]);
    }
}else{
    // criando novo usuário
    $users = UserRepository::getAll();
    if(count($users) >= $_ENV['REGISTER_LIMIT']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Limite máximo de cadastros atingido, tente novamente mais tarde.']);
        exit;
    }

    $tokenResCreate = UserRepository::createGoogle($userData);
    if($tokenResCreate){
        echo json_encode([
            'success' => true,
            'message' => 'Usuário criado e login bem-sucedido',
            'token' => $tokenResCreate
        ]);
    }else{
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao criar usuário com Google'
        ]);
    }
}