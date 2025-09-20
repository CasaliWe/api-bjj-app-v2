<?php

    // Headers CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, Cache-Control, Authorization, Origin");
    header("Content-Type: application/json; charset=UTF-8");

    // Se for preflight (OPTIONS), responde OK e sai
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    // Lista de IPs permitidos
    $ips_permitidos = [
        '*'
    ];

    // Se houver um asterisco (*) na lista, permite qualquer IP
    if (in_array('*', $ips_permitidos)) {
        // Acesso liberado para todos os IPs

    } else if (!in_array($_SERVER['REMOTE_ADDR'], $ips_permitidos)) {
        http_response_code(403); // Forbidden - código de status 403
        echo json_encode(['success' => false, 'message' => 'Acesso negado. IP não autorizado.']);
        exit;
    }

?>