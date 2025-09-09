<?php

namespace Repositories;

use Models\User;
use Models\Token;

use Core\Logger;

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

    // pegando usuário pelo email (verificar login, cadastro e atualizar senha)
    public static function getByEmail($email) {
        return User::where('email', $email)->select('email', 'senha')->first();
    }

    // criando novo usuário
    public static function create($dados) {

        // gerando o bjj_id
        $nome = strtolower(explode(' ', $dados['username'])[0]);
        // pegando a primeira letra nome
        $primeiraLetra = substr($nome, 0, 1);
        // gerando um numero aleatório de 4 digitos
        $numeroAleatorio = rand(1000, 9999);
        // juntando tudo
        $bjj_id = $primeiraLetra . $numeroAleatorio;

        // calculando 7 dias após a data atual do cadastro
        $dataAtual = new \DateTime();
        $dataExpiracao = $dataAtual->modify('+7 days');

        try {
            $res = User::create(
                [
                    'nome' => $dados['username'],
                    'email' => $dados['email'],
                    'senha' => password_hash($dados['password'], PASSWORD_BCRYPT),
                    'whatsapp' => '(00) 0 0000-0000',
                    'whatsapp_verificado' => 0,
                    'idade' => 18,
                    'peso' => 70,
                    'faixa' => 'Branca',
                    'academia' => 'Bjj Academy',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP',
                    'finalizacao' => 'Chave de Braço',
                    'bio' => 'Biografia',
                    'pais' => 'Brasil',
                    'perfilPublico' => 'Não',
                    'estilo' => 'Equilibrado',
                    'competidor' => 'Não',
                    'primeiroAcesso' => 1,
                    'plano' => 'Plus',
                    'vencimento' => $dataExpiracao->format('Y-m-d'),
                    'exp' => 0,
                    'bjj_id' => $bjj_id 
                ]
            );
    
            // gerando token de autenticação
            $tokenValue = bin2hex(random_bytes(16));
            Token::create([
                'user_id' => $res->id,
                'valor' => $tokenValue
            ]);

            // resposta para o user com token
            return $tokenValue;

        } catch (\Throwable $th) {
            Logger::log('Erro ao criar usuário: ' . $th->getMessage(), 'ERROR');
            return [
                'error' => 'Erro ao criar usuário',
                'details' => $th->getMessage()
            ];
        }
    }

    // função de login
    public static function login($dados) {
        $user = User::where('email', $dados['email'])->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Usuário não encontrado'];
        }

        if (!password_verify($dados['senha'], $user->senha)) {
            return ['success' => false, 'message' => 'Senha incorreta'];
        }

        // gerando token de autenticação
        $tokenValue = bin2hex(random_bytes(16));
        Token::create([
            'user_id' => $user->id,
            'valor' => $tokenValue
        ]);

        return [
            'success' => true,
            'message' => 'Login bem-sucedido',
            'token' => $tokenValue
        ];
    }

    // criando usuário com dados do Google
    public static function createGoogle($userData) {

        // gerando o bjj_id
        $nome = strtolower(explode(' ', $userData['name'])[0]);
        // pegando a primeira letra nome
        $primeiraLetra = substr($nome, 0, 1);
        // gerando um numero aleatório de 4 digitos
        $numeroAleatorio = rand(1000, 9999);
        // juntando tudo
        $bjj_id = $primeiraLetra . $numeroAleatorio;

        // calculando 7 dias após a data atual do cadastro
        $dataAtual = new \DateTime();
        $dataExpiracao = $dataAtual->modify('+7 days');


        try {
            $res = User::create(
                [
                    'nome' => $userData['name'],
                    'email' => $userData['email'],
                    'senha' => null,
                    'whatsapp' => '(00) 0 0000-0000',
                    'whatsapp_verificado' => 0,
                    'idade' => 18,
                    'peso' => 70,
                    'faixa' => 'Branca',
                    'imagem' => $userData['picture'],
                    'academia' => 'Bjj Academy',
                    'cidade' => 'São Paulo',
                    'estado' => 'SP',
                    'finalizacao' => 'Chave de Braço',
                    'bio' => 'Biografia',
                    'pais' => 'Brasil',
                    'perfilPublico' => 'Não',
                    'estilo' => 'Equilibrado',
                    'competidor' => 'Não',
                    'primeiroAcesso' => 1,
                    'plano' => 'Plus',
                    'vencimento' => $dataExpiracao->format('Y-m-d'),
                    'exp' => 0,
                    'bjj_id' => $bjj_id
                ]
            );
    
            // gerando token de autenticação
            $tokenValue = bin2hex(random_bytes(16));
            Token::create([
                'user_id' => $res->id,
                'valor' => $tokenValue
            ]);
    
            // resposta para o user com token
            return $tokenValue;

        } catch (\Throwable $th) {
            Logger::log('Erro ao criar usuário com Google: ' . $th->getMessage(), 'ERROR');
            return [
                'error' => 'Erro ao criar usuário com Google',
                'details' => $th->getMessage()
            ];
        }
    }


    // login com Google
    public static function loginGoogle($email) {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return ['success' => false, 'message' => 'Usuário não encontrado'];
        }

        // gerando token de autenticação
        $tokenValue = bin2hex(random_bytes(16));
        Token::create([
            'user_id' => $user->id,
            'valor' => $tokenValue
        ]);

        return [
            'success' => true,
            'message' => 'Login bem-sucedido',
            'token' => $tokenValue
        ];
    }

    // verificando se o token é válido (se for ele retorna true, se não false)
    public static function checkToken($token) {
        $tokenData = Token::where('valor', $token)->first();
        if(!$tokenData) {
            return false;
        }

        $user = User::where('id', $tokenData->user_id)->first();
        if(!$user) {
            return false;
        }

        return $user;
    }
}