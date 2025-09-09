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

// pegando o valor e o mes do corpo da requisição
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['valor']) || !isset($input['meses'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetros "valor" e "meses" são obrigatórios.']);
    exit;
}

$valor = $input['valor'];
$meses = $input['meses'];
$cpf   = $input['cpf'];

//************************************************************************** 
// começa o processo do pagamento PIX dividindo em functions
//**************************************************************************


// ⚙️ Configurações iniciais
$apiKey = $_ENV['ASAAS_KEY']; 
$baseUrl = $_ENV['ASAAS_URL']; 
$userAgent = $_ENV['NOME_SITE'];
$id_cliente = null; // receberá o id do cliente no Asaas


// 1. Verificar se já existe cliente com esse email, pegar o client id
$ch = curl_init("$baseUrl/customers?email=" . urlencode($user['email']));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "access_token: $apiKey",
        "User-Agent: $userAgent"
    ]
]);
$resposta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 400) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao consultar cliente no ASAAS',
        'details' => $resposta
    ]);
    exit;
}

$clientes = json_decode($resposta, true);
if(count($clientes['data']) > 0) {

    // pega o id do cliente existente e salva na variável global
    $id_cliente = $clientes['data'][0]['id'];

}else{
    
    // cria o cliente no Asaas e pega o id
    $cliente = [
        "name" => $user['nome'],
        "email" => $user['email'],
        "cpfCnpj" => $cpf           
    ];
    
    $ch = curl_init("$baseUrl/customers");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,                      // Recebe resposta como string
        CURLOPT_POST => true,                                // Método POST
        CURLOPT_POSTFIELDS => json_encode($cliente),         // Converte array PHP para JSON
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "access_token: $apiKey",                          // Chave de API do sandbox
            "User-Agent: $userAgent"                          // User-Agent para identificar o cliente
        ]
    ]);
    $resposta = curl_exec($ch);
    $clienteCriado = json_decode($resposta, true);           // Converte resposta JSON para array PHP
    curl_close($ch);
    
    if (!isset($clienteCriado['id'])) {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar cliente no Asaas.', 'details' => $clienteCriado]);
        exit;
    }else{
        $id_cliente = $clienteCriado['id'];
    }
}


// 2. Gerar cobrança PIX
$cobranca = [
    "customer" => $id_cliente,                            // ID do cliente criado agora
    "billingType" => "PIX",                               // Tipo de cobrança: Pix
    "value" => $valor,                                    // Valor da cobrança
    "dueDate" => date('Y-m-d', strtotime('+1 day')),      // Vencimento: amanhã
    "externalReference" => json_encode([
        'bjj_id'  => $user['bjj_id'],
        'meses'   => $meses,
    ])                                                    // Id o user do sistema
];

$ch = curl_init("$baseUrl/payments");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($cobranca),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "access_token: $apiKey",
        "User-Agent: $userAgent" 
    ]
]);
$resposta = curl_exec($ch);
$pix = json_decode($resposta, true);
curl_close($ch);

if($pix['id'] == null){
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao gerar cobrança PIX no ASAAS',
        'details' => $pix
    ]);
    exit;
}


// 3. Buscando detalhes do PIX
$paymentId = $pix['id'];
$ch = curl_init("$baseUrl/payments/$paymentId/pixQrCode");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "access_token: $apiKey",
        "User-Agent: $userAgent"
    ]
]);
$resposta = curl_exec($ch);
$infoPix = json_decode($resposta, true);
curl_close($ch);


echo json_encode([
    'success' => true,
    'message' => 'Cobrança PIX gerada com sucesso.',
    'qrcode' => $infoPix['encodedImage'],
    'pixCode' => $infoPix['payload'],
    'pix_id' => $pix['id']
]);






