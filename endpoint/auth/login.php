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

// chamando a função de login
use Repositories\UserRepository;
$login = UserRepository::login($data);

// retornando a resposta
header('Content-Type: application/json');
echo json_encode($login);