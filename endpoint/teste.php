<?php

include_once __DIR__ . '/../config/db.php';

//pegando dados do login
use Repositories\LoginRepository;
$login = LoginRepository::getLogin();

echo json_encode($login);