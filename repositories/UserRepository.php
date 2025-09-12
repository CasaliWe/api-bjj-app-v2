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
        return User::where('email', $email)->select('id', 'nome', 'email', 'senha')->first();
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

    
    // atualizando senha do usuário
    public static function updateSenha($id, $dados) {
        $user = User::where('id', $id)->first();
        if(!$user) {
            return false;
        }

        if($user->senha) {
            if(!isset($dados['senha_atual']) || !password_verify($dados['senha_atual'], $user->senha)) {
                return false;
            }
        }

        // atualizando a senha
        if(isset($dados['senha'])) {
            $nova_senha = password_hash($dados['senha'], PASSWORD_DEFAULT);

            return User::where('id', $id)->update(
                ['senha' => $nova_senha]
            );
        }
    }

    // recuperar senha do usuário
    public static function recuperarSenha($id, $email, $nome) {
        // gerando uma senha aleatória
        $nova_senha = bin2hex(random_bytes(4));

        // atualizando a senha no banco
        $update = User::where('id', $id)->update(
            ['senha' => password_hash($nova_senha, PASSWORD_DEFAULT)]
        );

        // importando o arquivo de envio de email
        if($update) {
            require_once __DIR__ . '/../helpers/envio-emails/recuperar-senha.php';
            $emailEnviado = sendRecoveryEmail($email, $nome, $nova_senha);

            if($emailEnviado) {
                return [
                    'success' => true,
                    'message' => 'Uma nova senha foi enviada para seu e-mail'
                ];
            } else {
                // Caso o e-mail não seja enviado, retorna a senha para o administrador
                // Em produção, talvez você queira reverter a alteração da senha
                Logger::log('Falha ao enviar e-mail de recuperação para: ' . $email, 'WARNING');
                return [
                    'success' => false,
                    'message' => 'Houve um problema ao enviar o e-mail de recuperação',
                    'admin_info' => 'Senha gerada: ' . $nova_senha
                ];
            }
        }

        Logger::log('Falha ao enviar e-mail de recuperação para: ' . $email, 'WARNING');
        return [
            'success' => false,
            'message' => 'Não foi possível atualizar a senha'
        ];
    }


    // atualizando plano
    public static function updatePlano($bjj_id, $dados) {
        return User::where('bjj_id', $bjj_id)->update($dados);
    }

    // pegando usuário pelo bjj_id
    public static function getByBjjId($bjj_id) {
        return User::where('bjj_id', $bjj_id)->first();
    }

    // enviando email de confirmação de plano ativado
    public static function sendEmailPlanoAtivado($email, $nome, $meses) {
        require_once __DIR__ . '/../helpers/envio-emails/plano-ativado.php';
        return sendPlanoAtivadoEmail($email, $nome, $meses);
    }

    // enviando email de aviso de plano próximo do vencimento
    public static function sendEmailPlanoProximoVencimento($email, $nome) {
        require_once __DIR__ . '/../helpers/envio-emails/plano-proximo-vencimento.php';
        return sendPlanoProximoVencimentoEmail($email, $nome);
    }

    // resetando o token 
    public static function resetToken() {
        // pegando todos os tokens
        $tokens = Token::all();

        // verificando se o token tem 30 dias ou mais contando a data de criação
        // salvar em uma var quantos foram deletados
        $deletados = 0;
        $hoje = new \DateTime();
        foreach ($tokens as $token) {
            $dataCriacao = new \DateTime($token->created_at);
            $intervalo = $hoje->diff($dataCriacao);
            if ($intervalo->days >= 1) {
                // deletando o token
                Token::where('id', $token->id)->delete();
                $deletados++;
            }
        }

        return [
            'success' => true,
            'message' => 'Tokens expirados removidos com sucesso',
            'data'  => $deletados . ' tokens removidos'
        ];
    }


    // função para checar assinaturas (vencimento de planos)
    public static function checkAssinaturas() {
        try {

            // -------------------------------------------
            // atualizando assinaturas vencidas
            // -------------------------------------------
            $usuarios = User::all();
            $hoje = new \DateTime();
            $atualizados = 0;

            foreach ($usuarios as $usuario) {
                if ($usuario->vencimento) {
                    $dataVencimento = new \DateTime($usuario->vencimento);
                    if ($hoje > $dataVencimento) {
                        // plano expirado, atualizando para Grátis
                        User::where('id', $usuario->id)->update([
                            'plano' => 'Grátis',
                            'vencimento' => null
                        ]);
                        $atualizados++;
                    }
                }
            }

            // -------------------------------------------
            // enviando email para usuários que faltam 1 dia para o vencimento
            // -------------------------------------------

            $avisados = 0;
            foreach ($usuarios as $usuario) {
                if ($usuario->vencimento) {
                    $dataVencimento = new \DateTime($usuario->vencimento);
                    $intervalo = $hoje->diff($dataVencimento);
                    if ($intervalo->days == 1 && $hoje < $dataVencimento) {
                        // faltando 1 dia para o vencimento, enviando email
                        self::sendEmailPlanoProximoVencimento($usuario->email, $usuario->nome);
                        $avisados++;
                    }
                }
            }


            return [
                'success' => true,
                'message' => 'Verificação de assinaturas concluída',
                'data' => $atualizados . ' usuários atualizados para Grátis, ' . $avisados . ' usuários avisados sobre o vencimento'
            ];

        } catch (\Throwable $th) {
            Logger::log('Erro ao verificar assinaturas: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao verificar assinaturas',
                'details' => $th->getMessage()
            ];
        }
    }


    // atualizando exp
    public static function updateExp($id, $dados) {
        // pegando o exp atual salvo do user e somando com o novo exp
        $user = User::where('id', $id)->first();
        if (!$user) {
            return false;
        }

        $user->exp += $dados['exp'];
        return $user->save();
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