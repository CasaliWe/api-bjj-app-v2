<?php

// Conn DB
include_once __DIR__ . '/../../config/db.php';

// processa as variáveis 
require __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

use Repositories\UserRepository;

// pega os dados do webhook
$input = file_get_contents("php://input");
$data = json_decode($input, true);


//************************************************************************** 
// confirmando pagamento
//**************************************************************************

if ($data['event'] == 'PAYMENT_RECEIVED') {

    // pega os dados do pagamento
    $dadosExterno = json_decode($data['payment']['externalReference'], true);
    $bjj_id = $dadosExterno['bjj_id'];
    $meses = $dadosExterno['meses'];

    // nova data de vencimento
    $dataAtual = new DateTime();
    $dataAtual->modify("+$meses months");
    $novaDataVencimento = $dataAtual->format('Y-m-d');

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

    // enviando email de confirmação
    $user = UserRepository::getByBjjId($bjj_id);

    // chamando a função de envio de email
    $res = UserRepository::sendEmailPlanoAtivado($user->email, $user->nome, $meses);


    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pagamento confirmado.'
    ]);
    exit;
}





