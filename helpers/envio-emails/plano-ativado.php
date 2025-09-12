<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendPlanoAtivadoEmail($email, $nome, $meses) {
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
        $mail->addAddress($email, $nome);
        $mail->addReplyTo($_ENV['MAIL_USER'], $_ENV['NOME_SITE']);

        // Cabeçalhos adicionais
        $mail->addCustomHeader('X-Mailer', 'PHP/' . phpversion());
        $mail->addCustomHeader('Precedence', 'bulk');

        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Plano Ativado - ' . $_ENV['NOME_SITE'];
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                <h2>Olá, ' . $nome . '!</h2>
                <p>Seu plano foi ativado com sucesso!</p>
                <p>Você agora tem acesso a todas as funcionalidades por ' . $meses . ' meses.</p>
                <img src="' . $_ENV['BASE_URL'] . 'admin/assets/imagens/site-admin/logo.png" alt="' . $_ENV['NOME_SITE'] . ' Logo" style="max-width: 200px; margin-top: 25px;">
            </div>
        ';

        $mail->AltBody = "Olá, $nome!\n\nSeu plano foi ativado com sucesso!\n\nVocê agora tem acesso a todas as funcionalidades por $meses meses.\n\nAtenciosamente,\nEquipe " . $_ENV['NOME_SITE'];

        $mail->send();

        Logger::log('E-mail de ativação de plano enviado com sucesso para ' . $email, 'INFO');
        return true;
    } catch (Exception $e) {
        Logger::log('Erro ao enviar e-mail de ativação de plano: ' . $mail->ErrorInfo, 'ERROR');
        return false;
    }
}