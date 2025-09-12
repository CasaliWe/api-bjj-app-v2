<?php

// Verifica IPs permitidos e configura CORS
include_once __DIR__ . '/../../helpers/ips-permitidos.php';

// Conn DB
include_once __DIR__ . '/../../config/db.php';

// processa as variáveis 
require __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
use Repositories\UserRepository;
use Models\User;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// verifica se o método da requisição é GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido. Use GET.']);
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
$user = UserRepository::checkToken($token);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}

// Consultar os dados necessários
// Função para formatar a data em português
function formatarDataPtBr($data) {
    if (!$data) return null;
    
    $meses = [
        'January' => 'Janeiro',
        'February' => 'Fevereiro',
        'March' => 'Março',
        'April' => 'Abril',
        'May' => 'Maio',
        'June' => 'Junho',
        'July' => 'Julho',
        'August' => 'Agosto',
        'September' => 'Setembro',
        'October' => 'Outubro',
        'November' => 'Novembro',
        'December' => 'Dezembro'
    ];
    
    $dataObj = date_create($data);
    $dia = date_format($dataObj, "d");
    $mesEn = date_format($dataObj, "F");
    
    return $dia . " de " . $meses[$mesEn];
}

$giData = [
    'total' => $user->treinos()->where('tipo', 'gi')->count(),
    'esteMes' => $user->treinos()->where('tipo', 'gi')->whereMonth('data', date('m'))->count(),
    'ultimaVez' => $user->treinos()->where('tipo', 'gi')->latest('data')->first() ? 
                  formatarDataPtBr($user->treinos()->where('tipo', 'gi')->latest('data')->first()->data) : null
];

$noGiData = [
    'total' => $user->treinos()->where('tipo', 'nogi')->count(),
    'esteMes' => $user->treinos()->where('tipo', 'nogi')->whereMonth('data', date('m'))->count(),
    'ultimaVez' => $user->treinos()->where('tipo', 'nogi')->latest('data')->first() ?
                  formatarDataPtBr($user->treinos()->where('tipo', 'nogi')->latest('data')->first()->data) : null
];

$competicoesGi = [
    'eventos' => $user->competicoes()->where('modalidade', 'gi')->count(),
    'lutas' => $user->competicoes()->where('modalidade', 'gi')->sum('numero_lutas'),
    'vitorias' => $user->competicoes()->where('modalidade', 'gi')->sum('numero_vitorias'),
    'derrotas' => $user->competicoes()->where('modalidade', 'gi')->sum('numero_derrotas'),
    'finalizacoes' => $user->competicoes()->where('modalidade', 'gi')->sum('numero_finalizacoes'),
    'primeiroLugar' => $user->competicoes()->where('modalidade', 'gi')->where('colocacao', 1)->count(),
    'segundoLugar' => $user->competicoes()->where('modalidade', 'gi')->where('colocacao', 2)->count(),
    'terceiroLugar' => $user->competicoes()->where('modalidade', 'gi')->where('colocacao', 3)->count()
];

$competicoesNoGi = [
    'eventos' => $user->competicoes()->where('modalidade', 'nogi')->count(),
    'lutas' => $user->competicoes()->where('modalidade', 'nogi')->sum('numero_lutas'),
    'vitorias' => $user->competicoes()->where('modalidade', 'nogi')->sum('numero_vitorias'),
    'derrotas' => $user->competicoes()->where('modalidade', 'nogi')->sum('numero_derrotas'),
    'finalizacoes' => $user->competicoes()->where('modalidade', 'nogi')->sum('numero_finalizacoes'),
    'primeiroLugar' => $user->competicoes()->where('modalidade', 'nogi')->where('colocacao', 1)->count(),
    'segundoLugar' => $user->competicoes()->where('modalidade', 'nogi')->where('colocacao', 2)->count(),
    'terceiroLugar' => $user->competicoes()->where('modalidade', 'nogi')->where('colocacao', 3)->count()
];

// Retornar os dados
http_response_code(200);
echo json_encode([
    'success' => true,
    'data' => [
        'gi' => $giData,
        'noGi' => $noGiData,
        'competicoesGi' => $competicoesGi,
        'competicoesNoGi' => $competicoesNoGi
    ]
]);
exit;