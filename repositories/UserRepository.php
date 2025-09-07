<?php

namespace Repositories;

use Models\User;
use Models\Token;

class UserRepository {
    // pegando todos os usuários
    public static function getAll() {
        return User::all();
    }

    // pegando único usuário
    public static function getOne($id) {
        return User::where('id', $id)->first();
    }

    // deletando usuário pelo id
    public static function delete($id) {
        return User::where('id', $id)->delete();
    }

    // atualizando usuário pelo id
    public static function update($id, $dados) {
        return User::where('id', $id)->update($dados);
    }

    // pegando usuário pelo email (verificar login e atualizar senha)
    public static function getByEmail($email) {
        return User::where('email', $email)->select('email', 'senha')->first();
    }

    // verificando se o token é válido (se for ele retorna true, se não false)
    public static function checkToken($token, $user_id) {
        $tokenData = Token::where('valor', $token)->first();
        if(!$tokenData) {
            return false;
        }

        $user = User::where('id', $tokenData->user_id)->first();
        if($user->id != $user_id) {
            return false;
        }
        
        return true;
    }
}