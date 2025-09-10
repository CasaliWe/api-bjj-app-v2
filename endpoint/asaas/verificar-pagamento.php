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
if (!isset($input['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Parâmetro "id" é obrigatório.']);
    exit;
}

//************************************************************************** 
// verificando o pagamento no asaas
//**************************************************************************


// ⚙️ Configurações iniciais
$apiKey = $_ENV['ASAAS_KEY']; 
$baseUrl = $_ENV['ASAAS_URL']; 
$userAgent = $_ENV['NOME_SITE'];
$id_pix = $input['id'];

// req para api asaas para verificar status do pedido
$ch = curl_init("$baseUrl/payments/$id_pix");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "access_token: $apiKey",
        "User-Agent: $userAgent"
    ]
]);
$resposta = curl_exec($ch);
$pagamento = json_decode($resposta, true);
curl_close($ch);

if(!$pagamento || isset($pagamento['errors'])) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao verificar o pagamento.']);
    exit;
}


// verificando o status do pagamento
if ($pagamento['status'] == 'PENDING') {
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pagamento ainda não confirmado.',
        'status' => $pagamento['status'],
        'pagamento' => false
    ]);
    exit;
}

// Exibe o status se caso ele ja estiver pago
if ($pagamento['status'] == 'RECEIVED') {

    // pega os dados do pagamento
    $dadosExterno = json_decode($pagamento['externalReference'], true);
    $bjj_id = $dadosExterno['bjj_id'];
    $meses = $dadosExterno['meses'];

    // nova data de vencimento
    $dataAtual = new DateTime();
    $dataAtual->modify("+$meses months");
    $novaDataVencimento = $dataAtual->format('Y-m-d');

    // verifica se o já foi pago e já está no plano Plus (se sim ja retorna)
    if($novaDataVencimento == $user['vencimento'] && $user['plano'] == 'Plus') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Pagamento já confirmado anteriormente.',
            'status' => $pagamento['status'],
            'pagamento' => true
        ]);
        exit;
    }

    
    // montando os dados
    $dadosParaAtualizar = [
        'plano' => 'Plus',
        'vencimento' => $novaDataVencimento
    ];

    // atualizando no banco
    $res = UserRepository::updatePlano($bjj_id, $dadosParaAtualizar);
    if(!$res) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o plano.']);
        exit;
    }

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pagamento confirmado.',
        'status' => $pagamento['status'],
        'pagamento' => true
    ]);
    exit;
}





