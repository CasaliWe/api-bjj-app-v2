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

// resetando o token
use Repositories\UserRepository;
$res = UserRepository::resetToken();

// se deu erro, retorna
if ($res['success'] === false) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $res['message']
    ]);
    exit;
}

// retorna os dados do usuário
http_response_code(200);
echo json_encode($res);
exit;