<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, Cache-Control, Authorization, Origin');

    // Lista de IPs permitidos
    $ips_permitidos = [
        '*'
    ];

    // Se houver um asterisco (*) na lista, permite qualquer IP
    if (in_array('*', $ips_permitidos)) {
        // Acesso liberado para todos os IPs

    } else if (!in_array($_SERVER['REMOTE_ADDR'], $ips_permitidos)) {
        http_response_code(403); // Forbidden - código de status 403
        echo json_encode(['error' => 'Acesso negado.']);
        exit;
    }

?>