<?php

require __DIR__ . '/../../vendor/autoload.php'; 
use Core\Logger;

$sourceDir = __DIR__ . '/../assets/imagens/arquivos/'; // Caminho da pasta original
$backupDir = __DIR__ . '/../backups/arquivos/uploads_backup_' . date('d-m-Y') . '/'; 

function copyDirectory($source, $destination) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }

    $files = scandir($source);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
        $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;

        if (is_dir($sourcePath)) {
            copyDirectory($sourcePath, $destinationPath);
        } else {
            copy($sourcePath, $destinationPath);
        }
    }
}

if (is_dir($sourceDir)) {
    copyDirectory($sourceDir, $backupDir);
    Logger::log("Backup das imagens criado com sucesso: $backupDir\n", 'INFO');
    echo "Backup das imagens criado com sucesso: $backupDir\n";
} else {
    Logger::log("A pasta de origem não existe.\n", 'ERROR');
    echo "A pasta de origem não existe.\n";
}
