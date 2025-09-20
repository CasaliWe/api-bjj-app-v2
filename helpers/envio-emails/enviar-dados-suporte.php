<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendSupportEmail($data) {
    // Carrega as variáveis de ambiente
    $dotenv = Dotenv::createImmutable(__DIR__.'/../../');
    $dotenv->load();

    // Inicializa o PHPMailer
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
        $mail->addAddress('wesleicasali18@gmail.com', 'Weslei Casali'); 
        $mail->addReplyTo($_ENV['MAIL_USER'], $_ENV['NOME_SITE']);

        // Cabeçalhos adicionais
        $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->addCustomHeader('Precedence', 'bulk');

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Novo contato do site (SUPORTE) - ' . $_ENV['NOME_SITE'];
        $mail->Body = '
            <h1>Novo contato do site (Suporte)!</h1>
            <p><strong>Nome:</strong> ' . $data['name'] . '</p>
            <p><strong>Email:</strong> ' . $data['email'] . '</p>
            <p><strong>Assunto:</strong> ' . $data['subject'] . '</p>
            <p><strong>Categoria:</strong> ' . $data['category'] . '</p>
            <p><strong>Mensagem:</strong> ' . $data['message'] . '</p>
        ';

        $mail->AltBody = "Olá, {$data['name']}!\n\nVocê recebeu uma nova mensagem de contato:\n\nAssunto: {$data['subject']}\nMensagem: {$data['message']}\n\nAtenciosamente,\nEquipe " . $_ENV['NOME_SITE'];

        $mail->send();

        Logger::log('E-mail de novo contato suporte enviado com sucesso para ' . $data['email'], 'INFO');
        return true;
    } catch (Exception $e) {
        Logger::log('Erro ao enviar e-mail de novo contato suporte: ' . $mail->ErrorInfo, 'ERROR');
        return false;
    }
}