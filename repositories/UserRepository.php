<?php

namespace Repositories;

use Models\User;
use Models\Token;
use Core\Logger;

class UserRepository {
    // pegando todos os usuários
    public static function getAll() {
        try {
            return User::all();
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar todos os usuários: ' . $th->getMessage(), 'ERROR');
            return [];
        }
    }

    // pegando único usuário
    public static function getOne($id) {
        try {
            return User::where('id', $id)->first();
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return null;
        }
    }

    // deletando usuário pelo id
    public static function delete($id) {
        try {
            return User::where('id', $id)->delete();
        } catch (\Throwable $th) {
            Logger::log('Erro ao deletar usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // atualizando usuário pelo id
    public static function update($id, $dados) {
        try {
            return User::where('id', $id)->update($dados);
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // pegando usuário pelo email (verificar login, cadastro e atualizar senha)
    public static function getByEmail($email) {
        try {
            return User::where('email', $email)->select('id', 'nome', 'email', 'senha')->first();
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar usuário por email ' . $email . ': ' . $th->getMessage(), 'ERROR');
            return null;
        }
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
        try {
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
        } catch (\Throwable $th) {
            Logger::log('Erro ao fazer login para email ' . $dados['email'] . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro interno no login',
                'details' => $th->getMessage()
            ];
        }
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
        try {
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
        } catch (\Throwable $th) {
            Logger::log('Erro ao fazer login Google para email ' . $email . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro interno no login Google',
                'details' => $th->getMessage()
            ];
        }
    }

    
    // atualizando senha do usuário
    public static function updateSenha($id, $dados) {
        try {
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
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar senha do usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // recuperar senha do usuário
    public static function recuperarSenha($id, $email, $nome) {
        try {
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

            Logger::log('Falha ao atualizar senha para recuperação - email: ' . $email, 'WARNING');
            return [
                'success' => false,
                'message' => 'Não foi possível atualizar a senha'
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao recuperar senha do usuário ID ' . $id . ' email ' . $email . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro interno ao recuperar senha',
                'details' => $th->getMessage()
            ];
        }
    }


    // atualizando plano
    public static function updatePlano($bjj_id, $dados) {
        try {
            return User::where('bjj_id', $bjj_id)->update($dados);
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar plano do usuário BJJ_ID ' . $bjj_id . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // pegando usuário pelo bjj_id
    public static function getByBjjId($bjj_id) {
        try {
            return User::where('bjj_id', $bjj_id)->first();
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar usuário por BJJ_ID ' . $bjj_id . ': ' . $th->getMessage(), 'ERROR');
            return null;
        }
    }

    // enviando email de confirmação de plano ativado
    public static function sendEmailPlanoAtivado($email, $nome, $meses) {
        try {
            require_once __DIR__ . '/../helpers/envio-emails/plano-ativado.php';
            return sendPlanoAtivadoEmail($email, $nome, $meses);
        } catch (\Throwable $th) {
            Logger::log('Erro ao enviar email de plano ativado para ' . $email . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // enviando email de aviso de plano próximo do vencimento
    public static function sendEmailPlanoProximoVencimento($email, $nome) {
        try {
            require_once __DIR__ . '/../helpers/envio-emails/plano-proximo-vencimento.php';
            return sendPlanoProximoVencimentoEmail($email, $nome);
        } catch (\Throwable $th) {
            Logger::log('Erro ao enviar email de plano próximo vencimento para ' . $email . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // resetando o token 
    public static function resetToken() {
        try {
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
        } catch (\Throwable $th) {
            Logger::log('Erro ao resetar tokens: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao resetar tokens',
                'details' => $th->getMessage()
            ];
        }
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


    // buscando objetivos do usuário
    public static function buscarObjetivosUser($id) {
        try {
            $user = User::where('id', $id)->first();
            if(!$user) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado',
                    'data' => null
                ];
            }

            // Buscar objetivos salvos no usuário (nomes reais do banco)
            $tecnicasMeta = isset($user->tecnica_objetivo) ? (int)$user->tecnica_objetivo : 0;
            $treinosMeta = isset($user->treino_objetivo) ? (int)$user->treino_objetivo : 0;
            $competicoesMeta = isset($user->competicao_objetivo) ? (int)$user->competicao_objetivo : 0;

            // Datas para filtro do mês atual
            $inicioMes = date('Y-m-01');
            $fimMes = date('Y-m-t');

            // Técnicas do mês
            $tecnicas = \Models\Tecnicas::where('usuario_id', $id)
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->count();

            // Treinos do mês
            $treinos = \Models\Treino::where('usuario_id', $id)
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->count();

            // Competições do mês
            $competicoes = \Models\Competicoes::where('user_id', $id)
                ->whereBetween('created_at', [$inicioMes, $fimMes])
                ->count();

            // Observações do mês (campo correto: data)
            $observacoesMes = \Models\Observacoes::where('usuario_id', $id)
                ->whereBetween('data', [$inicioMes, $fimMes])
                ->count();

            // Observações total
            $observacoesTotal = \Models\Observacoes::where('usuario_id', $id)
                ->count();

            // Cálculos faltando e up/down
            $tecnicasFaltando = max($tecnicasMeta - $tecnicas, 0);
            $treinosFaltando = max($treinosMeta - $treinos, 0);
            $competicoesFaltando = max($competicoesMeta - $competicoes, 0);

            $upDownTecnicas = ($tecnicasMeta - $tecnicas) > 0 ? 'down' : 'up';
            $upDownTreinos = ($treinosMeta - $treinos) > 0 ? 'down' : 'up';
            $upDownCompeticoes = ($competicoesMeta - $competicoes) > 0 ? 'down' : 'up';

            // Monta resposta
            return [
                'success' => true,
                'message' => 'Objetivos do usuário obtidos com sucesso',
                'data' => [
                    'tecnicas' => (string)$tecnicas,
                    'tecnicasMeta' => (string)$tecnicasMeta,
                    'tecnicasFaltando' => (string)$tecnicasFaltando,
                    'upDownTecnicas' => $upDownTecnicas,
                    'treinos' => (string)$treinos,
                    'treinosMeta' => (string)$treinosMeta,
                    'treinosFaltando' => (string)$treinosFaltando,
                    'upDownTreinos' => $upDownTreinos,
                    'competicoes' => (string)$competicoes,
                    'competicoesMeta' => (string)$competicoesMeta,
                    'competicoesFaltando' => (string)$competicoesFaltando,
                    'upDownCompeticoes' => $upDownCompeticoes,
                    'observacoesTotal' => (string)$observacoesTotal,
                    'observacoesMes' => (string)$observacoesMes
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar objetivos do usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao buscar objetivos do usuário',
                'data' => null
            ];
        }
    }


    // atualizando exp
    public static function updateExp($id, $dados) {
        try {
            // pegando o exp atual salvo do user e somando com o novo exp
            $user = User::where('id', $id)->first();
            if (!$user) {
                return false;
            }

            $user->exp += $dados['exp'];
            return $user->save();
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar EXP do usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }

    // atualizando objetivos
    public static function updateObjetivos($id, $dados) {
        try {
            // pegando o usuário
            $user = User::where('id', $id)->first();
            if (!$user) {
                return false;
            }

            // atualizando os objetivos
            if (isset($dados['tecnicasMeta'])) {
                $user->tecnica_objetivo = (int)$dados['tecnicasMeta'];
            }
            if (isset($dados['treinosMeta'])) {
                $user->treino_objetivo = (int)$dados['treinosMeta'];
            }
            if (isset($dados['competicoesMeta'])) {
                $user->competicao_objetivo = (int)$dados['competicoesMeta'];
            }

            return [
                'success' => true,
                'message' => 'Objetivos atualizados com sucesso',
                'data' => $user->save()
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar objetivos do usuário ID ' . $id . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao atualizar objetivos',
                'details' => $th->getMessage()
            ];
        }
    }


    // buscando usuários por pelo tipo_pesquisa (nome ou bjj_id)
    public static function search($query, $tipo_pesquisa) {
        try {
            $tipo_pesquisa = strtolower($tipo_pesquisa);
            if ($tipo_pesquisa === 'nome') {
                $user = User::where('nome', 'LIKE', '%' . $query . '%')
                    ->select('id', 'nome', 'faixa', 'academia', 'cidade', 'estado', 'pais', 'bjj_id', 'imagem')
                    ->get();

                if ($user->isEmpty()) {
                    return [
                        'success' => false,
                        'message' => 'Nenhum usuário encontrado',
                        'data' => []
                    ];
                }

                // montando o user de retorno e salvando na variável
                $users_retorno = [];
                foreach ($user as $u) { 
                    $users_retorno[] = [
                        'id' => $u->id,
                        'nome' => $u->nome,
                        'faixa' => $u->faixa,
                        'academia' => $u->academia,
                        'cidade' => $u->cidade,
                        'estado' => $u->estado,
                        'pais' => $u->pais,
                        'bjj_id' => $u->bjj_id,
                        'imagem' => (!empty($u->imagem)) ? $_ENV['BASE_URL'] . 'admin/assets/imagens/arquivos/perfil/' . $u->imagem : null
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Usuários encontrados com sucesso',
                    'data' => $users_retorno
                ];
            }else{
                $user = User::where('bjj_id', $query)
                    ->select('id', 'nome', 'faixa', 'academia', 'cidade', 'estado', 'pais', 'bjj_id', 'imagem')
                    ->first();

                if (!$user) {
                    return [
                        'success' => false,
                        'message' => 'Usuário não encontrado',
                        'data' => []
                    ];
                }

                // Criando um array com um único objeto para manter consistência
                $users_retorno = [
                    [
                        'id' => $user->id,
                        'nome' => $user->nome,
                        'faixa' => $user->faixa,
                        'academia' => $user->academia,
                        'cidade' => $user->cidade,
                        'estado' => $user->estado,
                        'pais' => $user->pais,
                        'bjj_id' => $user->bjj_id,
                        'imagem' => (!empty($user->imagem)) ? $_ENV['BASE_URL'] . 'admin/assets/imagens/arquivos/perfil/' . $user->imagem : null
                    ]
                ];

                return [
                    'success' => true,
                    'message' => 'Usuário encontrado com sucesso',
                    'data' => $users_retorno
                ];
            }
        } catch (\Throwable $th) {
            Logger::log('Erro ao buscar usuários com query "' . $query . '" tipo "' . $tipo_pesquisa . '": ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao buscar usuários',
                'data' => []
            ];
        }
    }

    // verificando se o token é válido (se for ele retorna true, se não false)
    public static function checkToken($token) {
        try {
            $tokenData = Token::where('valor', $token)->first();
            if(!$tokenData) {
                return false;
            }

            $user = User::where('id', $tokenData->user_id)->first();
            if(!$user) {
                return false;
            }

            return $user;
        } catch (\Throwable $th) {
            Logger::log('Erro ao verificar token: ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }
}