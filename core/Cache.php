<?php

namespace Core;

use Predis\Client; 

class Cache {
    // Instância do cliente Redis acessível em toda a classe
    private static $redis;

    // Conectar ao Redis se não estiver conectado
    private static function conn() {
       if(!self::$redis) {
            self::$redis = new Client([
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',  
                'port'   => 6379,         
            ]);
        }
    }

    // Obter um valor do cache
    public static function get($key) {
        self::conn();
        return self::$redis->get($key);
    }

    // Definir um valor no cache com tempo de expiração (em segundos)
    public static function set($key, $value, $ttl = 15) {
        self::conn();
        self::$redis->setex($key, $ttl, $value);
    }

    // Remover um valor do cache
    public static function delete($key) {
        self::conn();
        self::$redis->del($key);
    }
}
