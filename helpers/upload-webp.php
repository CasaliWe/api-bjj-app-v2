<?php

// Verifica se o IP é permitido
include_once __DIR__ . '/ips-permitidos.php';

/**
 * Função genérica para converter imagens enviadas via formulário em WebP.
 * Pode ser usada em qualquer projeto PHP.
 *
 * @param array $arquivo        O array vindo de $_FILES['input']
 * @param string $pastaDestino  Caminho absoluto onde a imagem será salva
 * @param string $prefixo       Prefixo do nome do arquivo gerado (opcional)
 * @return string|null          Nome do arquivo WebP salvo, ou null se nenhum arquivo foi enviado
 */
function salvarImagemWebP(array $arquivo, string $pastaDestino, string $prefixo = 'upload-'): ?string
{
    if (!isset($arquivo) || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        die("Erro no upload: código " . $arquivo['error']);
    }

    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $hash = bin2hex(random_bytes(3));
    $nomeWebP = $prefixo . $hash . ".webp";
    $caminhoWebP = rtrim($pastaDestino, '/') . '/' . $nomeWebP;
    $caminhoTemporario = rtrim($pastaDestino, '/') . '/' . $hash . '.' . $extensao;

    if (!move_uploaded_file($arquivo['tmp_name'], $caminhoTemporario)) {
        die("Erro ao mover o arquivo enviado.");
    }

    if ($extensao === 'png') {
        $img = imagecreatefrompng($caminhoTemporario);
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    } elseif ($extensao === 'jpg' || $extensao === 'jpeg') {
        $img = imagecreatefromjpeg($caminhoTemporario);
    } elseif ($extensao === 'webp') {
        rename($caminhoTemporario, $caminhoWebP);
        return $nomeWebP;
    } else {
        unlink($caminhoTemporario);
        die("Formato não suportado: .$extensao");
    }

    imagewebp($img, $caminhoWebP, 80);
    imagedestroy($img);
    unlink($caminhoTemporario);

    return $nomeWebP;
}
