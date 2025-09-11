<?php

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

/**
 * Converte uma imagem para o formato WebP.
 * Utilizada para processar imagens de arquivos temporários após upload.
 * 
 * @param string $sourcePath    Caminho do arquivo de origem (temporário)
 * @param string $targetPath    Caminho completo onde o arquivo WebP será salvo
 * @param int $quality          Qualidade da imagem WebP (0-100)
 * @return bool                 True se a conversão foi bem sucedida, False caso contrário
 */
function convertImageToWebP(string $sourcePath, string $targetPath, int $quality = 80): bool
{
    if (!file_exists($sourcePath)) {
        return false;
    }

    // Determina o tipo de imagem
    $imageInfo = getimagesize($sourcePath);
    if ($imageInfo === false) {
        return false;
    }

    $mimeType = $imageInfo['mime'];
    
    // Cria uma nova imagem com base no tipo do arquivo
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            // Se já for WebP, apenas copia o arquivo
            return copy($sourcePath, $targetPath);
        default:
            return false;
    }
    
    if (!$image) {
        return false;
    }
    
    // Converte para WebP
    $result = imagewebp($image, $targetPath, $quality);
    
    // Libera a memória
    imagedestroy($image);
    
    return $result;
}
