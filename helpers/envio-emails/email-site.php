<?php

// Verifica se o IP é permitido ***
include_once __DIR__ . '/../ips-permitidos.php';


require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__.'/../../');
$dotenv->load();

use Core\Logger;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$mensagem = $_POST['mensagem'];


$respostaSuccess = [
    "success" => true,
    "message" => "E-mail enviado!"
];

$respostaError = [
    "success" => false,
    "message" => "E-mail não enviado!"
];

//enviar email
$mail = new PHPMailer(true);

try {
    // Configurações do servidor
    $mail->isSMTP();
    $mail->Host = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['MAIL_USER'];
    $mail->Password = $_ENV['MAIL_PASSWORD'];
    $mail->SMTPSecure = $_ENV['MAIL_SECURE'];
    $mail->Port = $_ENV['MAIL_PORT'];

    // Configuração da codificação de caracteres
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // Destinatários
    $mail->setFrom($_ENV['MAIL_USER'], $_ENV['NOME_SITE']);
    $mail->addAddress($_ENV['MAIL_USER_ADMIN'], $_ENV['NOME_SITE']);
    $mail->addReplyTo($_ENV['MAIL_USER'], $_ENV['NOME_SITE']);

    // Cabeçalhos adicionais
    $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
    $mail->addCustomHeader('Precedence', 'bulk');

    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Novo contato do site!';
    $mail->Body = '
        <h2>Dados do contato:</h2>
        <p style="margin-top:-5px; color: black;">Nome: ' . $nome . '</p>
        <p style="margin-top:-5px; color: black;">E-mail: ' . $email . '</p>
        <p style="margin-top:-5px; color: black;">Telefone: ' . $telefone . '</p>
        <p style="margin-top:-5px; color: black;">Mensagem: ' . $mensagem . '</p>
    ';

    $mail->send();

    Logger::log('E-mail enviado com sucesso para ' . $_ENV['MAIL_USER_ADMIN'] . '!', 'INFO');
    echo json_encode([$respostaSuccess]);
    exit;

} catch (Exception $e) {
    Logger::log('Erro ao enviar e-mail: ' . $mail->ErrorInfo, 'ERROR');
    echo json_encode([$respostaSuccess, $e]);
    exit;
}