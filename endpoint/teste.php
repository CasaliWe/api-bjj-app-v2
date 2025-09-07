<?php

include_once __DIR__ . '/../config/db.php';

//pegando dados do login
use Repositories\UserRepository;
$user = UserRepository::getAll();

echo json_encode($user);