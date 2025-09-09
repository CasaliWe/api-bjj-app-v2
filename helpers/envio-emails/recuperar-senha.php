<?php

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Logger;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendRecoveryEmail($email, $nome, $novaSenha) {
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
        $mail->Subject = 'Recuperação de Senha - ' . $_ENV['NOME_SITE'];
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;">
                <h2>Olá, ' . $nome . '!</h2>
                <p>Você solicitou a recuperação de senha para sua conta.</p>
                <p>Sua nova senha temporária é: <strong>' . $novaSenha . '</strong></p>
                <p>Por motivos de segurança, recomendamos que você altere esta senha após fazer login.</p>
                <p style="margin-top: 30px;">Atenciosamente,<br>Equipe ' . $_ENV['NOME_SITE'] . '</p>
                <img src="' . $_ENV['BASE_URL'] . 'admin/assets/imagens/site-admin/logo.png" alt="' . $_ENV['NOME_SITE'] . ' Logo" style="max-width: 200px; margin-top: 25px;">
            </div>
        ';

        $mail->AltBody = "Olá, $nome!\n\nVocê solicitou a recuperação de senha para sua conta.\n\nSua nova senha temporária é: $novaSenha\n\nPor motivos de segurança, recomendamos que você altere esta senha após fazer login.\n\nAtenciosamente,\nEquipe " . $_ENV['NOME_SITE'];

        $mail->send();
        
        Logger::log('E-mail de recuperação de senha enviado com sucesso para ' . $email, 'INFO');
        return true;
    } catch (Exception $e) {
        Logger::log('Erro ao enviar e-mail de recuperação de senha: ' . $mail->ErrorInfo, 'ERROR');
        return false;
    }
}