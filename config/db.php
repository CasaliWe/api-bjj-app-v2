<?php

// **********AUTOLOAD**********
require __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

// **********ELOQUENT**********
use Illuminate\Database\Capsule\Manager as Capsule;
$capsule = new Capsule;
$capsule->addConnection([
    'driver'   => 'mysql',
    'host'     => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'prefix'   => '',
    'options'  => [
        PDO::ATTR_PERSISTENT => true, 
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30, 
    ],
    'pool' => [
        'max' => 50,   
        'min' => 5     
    ],
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();