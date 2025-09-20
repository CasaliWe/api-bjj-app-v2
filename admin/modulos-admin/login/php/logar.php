<?php

session_start();

require __DIR__ . '/../../../../config/db.php';
use Repositories\LoginRepository;
use Core\Logger;



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // buscando login no banco
    $dadosLogin = LoginRepository::getLogin();

    // testando login
    if(filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS) == $dadosLogin['login'] && filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS) == $dadosLogin['senha']){
        $_SESSION['login'] = true;
        Logger::log("Login realizado com sucesso!", "INFO");
        header("Location: ../../../dashboard.php");
        exit;
    }else{
        $_SESSION['erro-login'] = true;
        Logger::log("Falha ao realizar login, USUÁRIO OU SENHA!", "WARNING");
        header("Location: ../../../index.php");
        exit;
    }

}

$conn->close();

?>
