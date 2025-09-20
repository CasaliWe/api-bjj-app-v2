<?php

namespace Repositories;

use Models\Competicoes;
use Models\CompeticoesImagens;
use Core\Logger;

class CompeticoesRepository {
    
    /**
     * Obtém todas as competições do usuário com filtros e paginação
     * 
     * @param int $userId ID do usuário autenticado
     * @param array $filtros Filtros a serem aplicados (modalidade, busca)
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Competições filtradas e informações de paginação
     */
    public static function listar($userId, $filtros = [], $pagina = 1, $limite = 10) {
        try {
            // Inicia a consulta básica para as competições do usuário
            $query = Competicoes::where('user_id', $userId);
            
            // Aplica filtro por modalidade se especificado
            if (isset($filtros['modalidade']) && !empty($filtros['modalidade'])) {
                $query->where('modalidade', $filtros['modalidade']);
            }
            
            // Aplica filtro de busca por termo se especificado
            if (isset($filtros['busca']) && !empty($filtros['busca'])) {
                $busca = '%' . $filtros['busca'] . '%';
                $query->where(function($q) use ($busca) {
                    $q->where('nome_evento', 'LIKE', $busca)
                      ->orWhere('cidade', 'LIKE', $busca)
                      ->orWhere('colocacao', 'LIKE', $busca)
                      ->orWhere('observacoes', 'LIKE', $busca);
                });
            }
            
            // Obtém o total de itens para a paginação
            $totalItems = $query->count();
            
            // Calcula o total de páginas
            $totalPages = ceil($totalItems / $limite);
            
            // Ajusta a página atual se necessário
            $pagina = max(1, min($pagina, $totalPages ?: 1));
            
            // Calcula o offset para a consulta
            $offset = ($pagina - 1) * $limite;
            
            // Obtém as competições com paginação, ordenadas por data de criação mais recente
            $competicoes = $query->with(['imagens'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata as competições para retorno
            $competicoesFormatadas = [];
            foreach ($competicoes as $competicao) {
                $imagensUrls = [];
                
                // Formata URLs das imagens com ID
                foreach ($competicao->imagens as $imagem) {
                    $imagensUrls[] = [
                        'id' => $imagem->id,
                        'url' => $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $imagem->url
                    ];
                }
                
                $competicoesFormatadas[] = [
                    'id' => $competicao->id,
                    'nomeEvento' => $competicao->nome_evento,
                    'cidade' => $competicao->cidade,
                    'data' => $competicao->data,
                    'modalidade' => $competicao->modalidade,
                    'colocacao' => $competicao->colocacao,
                    'categoria' => $competicao->categoria,
                    'numeroLutas' => (int)$competicao->numero_lutas,
                    'numeroVitorias' => (int)$competicao->numero_vitorias,
                    'numeroDerrotas' => (int)$competicao->numero_derrotas,
                    'numeroFinalizacoes' => (int)$competicao->numero_finalizacoes,
                    'observacoes' => $competicao->observacoes,
                    'isPublico' => (bool)$competicao->is_publico,
                    'imagens' => $imagensUrls
                ];
            }
            
            // Monta a resposta com as competições e informações de paginação
            return [
                'status' => 'success',
                'message' => 'Competições listadas com sucesso',
                'data' => [
                    'competicoes' => $competicoesFormatadas,
                    'pagination' => [
                        'currentPage' => (int)$pagina,
                        'totalPages' => (int)$totalPages,
                        'totalItems' => (int)$totalItems,
                        'itemsPerPage' => (int)$limite
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao listar competições: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao listar competições',
                'errorCode' => 500,
                'data' => [
                    'competicoes' => [],
                    'pagination' => [
                        'currentPage' => 1,
                        'totalPages' => 0,
                        'totalItems' => 0,
                        'itemsPerPage' => $limite
                    ]
                ]
            ];
        }
    }
    
    /**
     * Obtém as competições públicas da comunidade com paginação
     * 
     * @param array $filtros Filtros a serem aplicados (modalidade, busca)
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Competições públicas e informações de paginação
     */
    public static function listarComunidade($filtros = [], $pagina = 1, $limite = 10) {
        try {
            // Inicia a consulta para competições públicas
            $query = Competicoes::where('is_publico', true);
            
            // Aplica filtro por modalidade se especificado
            if (isset($filtros['modalidade']) && !empty($filtros['modalidade'])) {
                $query->where('modalidade', $filtros['modalidade']);
            }
            
            // Aplica filtro de busca por termo se especificado
            if (isset($filtros['busca']) && !empty($filtros['busca'])) {
                $busca = '%' . $filtros['busca'] . '%';
                $query->where(function($q) use ($busca) {
                    $q->where('nome_evento', 'LIKE', $busca)
                      ->orWhere('cidade', 'LIKE', $busca)
                      ->orWhere('colocacao', 'LIKE', $busca)
                      ->orWhere('observacoes', 'LIKE', $busca);
                });
            }
            
            // Obtém o total de itens para a paginação
            $totalItems = $query->count();
            
            // Calcula o total de páginas
            $totalPages = ceil($totalItems / $limite);
            
            // Ajusta a página atual se necessário
            $pagina = max(1, min($pagina, $totalPages ?: 1));
            
            // Calcula o offset para a consulta
            $offset = ($pagina - 1) * $limite;
            
            // Obtém as competições com paginação, ordenadas por data de criação mais recente
            $competicoes = $query->with(['imagens', 'user'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata as competições para retorno
            $competicoesFormatadas = [];
            foreach ($competicoes as $competicao) {
                $imagensUrls = [];
                
                // Formata URLs das imagens
                foreach ($competicao->imagens as $imagem) {
                    $imagensUrls[] = [
                        'id' => $imagem->id,
                        'url' => $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $imagem->url
                    ];
                }

                // montando a url da imagem do usuário
                $perfil_imagem = null;
                if ($competicao->user->imagem) {
                    $perfil_imagem = $baseUrl . 'admin/assets/imagens/arquivos/perfil/' . $competicao->user->imagem;
                }
                
                // Informações do usuário
                $usuario = [
                    'id' => $competicao->user->id,
                    'nome' => $competicao->user->nome,
                    'foto' => $perfil_imagem,
                    'faixa' => $competicao->user->faixa,
                    'bjj_id' => $competicao->user->bjj_id
                ];
                
                $competicoesFormatadas[] = [
                    'id' => $competicao->id,
                    'nomeEvento' => $competicao->nome_evento,
                    'cidade' => $competicao->cidade,
                    'data' => $competicao->data,
                    'modalidade' => $competicao->modalidade,
                    'colocacao' => $competicao->colocacao,
                    'categoria' => $competicao->categoria,
                    'numeroLutas' => (int)$competicao->numero_lutas,
                    'numeroVitorias' => (int)$competicao->numero_vitorias,
                    'numeroDerrotas' => (int)$competicao->numero_derrotas,
                    'numeroFinalizacoes' => (int)$competicao->numero_finalizacoes,
                    'observacoes' => $competicao->observacoes,
                    'usuario' => $usuario,
                    'imagens' => $imagensUrls
                ];
            }
            
            // Monta a resposta com as competições e informações de paginação
            return [
                'status' => 'success',
                'message' => 'Competições da comunidade listadas com sucesso',
                'data' => [
                    'competicoes' => $competicoesFormatadas,
                    'pagination' => [
                        'currentPage' => (int)$pagina,
                        'totalPages' => (int)$totalPages,
                        'totalItems' => (int)$totalItems,
                        'itemsPerPage' => (int)$limite
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao listar competições da comunidade: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao listar competições da comunidade',
                'errorCode' => 500,
                'data' => [
                    'competicoes' => [],
                    'pagination' => [
                        'currentPage' => 1,
                        'totalPages' => 0,
                        'totalItems' => 0,
                        'itemsPerPage' => $limite
                    ]
                ]
            ];
        }
    }
    
    /**
     * Cria uma nova competição
     * 
     * @param array $dados Dados da competição (nomeEvento, cidade, data, etc)
     * @param int $userId ID do usuário autenticado
     * @param array $imagens Array com nomes das imagens (opcional)
     * @return array Competição criada ou mensagem de erro
     */
    public static function criar($dados, $userId, $imagens = []) {
        try {            
            // Cria a nova competição
            $competicao = Competicoes::create([
                'user_id' => $userId,
                'nome_evento' => $dados['nomeEvento'],
                'cidade' => $dados['cidade'] ?? '',
                'data' => $dados['data'] ?? null,
                'modalidade' => $dados['modalidade'] ?? '',
                'colocacao' => $dados['colocacao'] ?? '',
                'categoria' => $dados['categoria'] ?? '',
                'numero_lutas' => $dados['numeroLutas'] ?? 0,
                'numero_vitorias' => $dados['numeroVitorias'] ?? 0,
                'numero_derrotas' => $dados['numeroDerrotas'] ?? 0,
                'numero_finalizacoes' => $dados['numeroFinalizacoes'] ?? 0,
                'observacoes' => $dados['observacoes'] ?? '',
                'is_publico' => isset($dados['isPublico']) ? $dados['isPublico'] : false
            ]);
            
            // Processa e adiciona as imagens, se houver
            $imagensUrls = [];
            $baseUrl = $_ENV['BASE_URL'];
            
            if (!empty($imagens)) {
                foreach ($imagens as $nomeImagem) {
                    // Adiciona a imagem ao banco
                    $imagemObj = CompeticoesImagens::create([
                        'competicao_id' => $competicao->id,
                        'url' => $nomeImagem
                    ]);
                    
                    // Adiciona a URL formatada ao array de resposta
                    $imagensUrls[] = [
                        'id' => $imagemObj->id,
                        'url' => $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $nomeImagem
                    ];
                }
            }
            
            // Formata a resposta
            return [
                'status' => 'success',
                'message' => 'Competição criada com sucesso',
                'data' => [
                    'id' => $competicao->id,
                    'nomeEvento' => $competicao->nome_evento,
                    'cidade' => $competicao->cidade,
                    'data' => $competicao->data,
                    'modalidade' => $competicao->modalidade,
                    'colocacao' => $competicao->colocacao,
                    'numeroLutas' => (int)$competicao->numero_lutas,
                    'numeroVitorias' => (int)$competicao->numero_vitorias,
                    'numeroDerrotas' => (int)$competicao->numero_derrotas,
                    'numeroFinalizacoes' => (int)$competicao->numero_finalizacoes,
                    'observacoes' => $competicao->observacoes,
                    'isPublico' => (bool)$competicao->is_publico,
                    'imagens' => $imagensUrls
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao criar competição: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao criar competição',
                'errorCode' => 500,
                'data' => null
            ];
        }
    }
    
    /**
     * Atualiza uma competição existente
     * Verifica se a competição pertence ao usuário autenticado
     * 
     * @param array $dados Dados atualizados da competição
     * @param int $userId ID do usuário autenticado
     * @param array $imagens Array com nomes das novas imagens (opcional)
     * @param array $imagensExistentes Array com IDs das imagens a manter (opcional)
     * @return array Competição atualizada ou mensagem de erro
     */
    public static function atualizar($dados, $userId, $imagens = [], $imagensExistentes = []) {
        try {            
            // Busca a competição com verificação de propriedade
            $competicao = Competicoes::where('id', $dados['id'])
                          ->where('user_id', $userId)
                          ->first();
            
            if (!$competicao) {
                return [
                    'status' => 'error',
                    'message' => 'Competição não encontrada ou você não tem permissão para editá-la',
                    'errorCode' => 404,
                    'data' => null
                ];
            }
            
            // Atualiza os dados
            $competicao->nome_evento = $dados['nomeEvento'];
            $competicao->cidade = $dados['cidade'] ?? $competicao->cidade;
            $competicao->data = $dados['data'] ?? $competicao->data;
            $competicao->modalidade = $dados['modalidade'] ?? $competicao->modalidade;
            $competicao->colocacao = $dados['colocacao'] ?? $competicao->colocacao;
            $competicao->categoria = $dados['categoria'] ?? $competicao->categoria;
            $competicao->numero_lutas = $dados['numeroLutas'] ?? $competicao->numero_lutas;
            $competicao->numero_vitorias = $dados['numeroVitorias'] ?? $competicao->numero_vitorias;
            $competicao->numero_derrotas = $dados['numeroDerrotas'] ?? $competicao->numero_derrotas;
            $competicao->numero_finalizacoes = $dados['numeroFinalizacoes'] ?? $competicao->numero_finalizacoes;
            $competicao->observacoes = $dados['observacoes'] ?? $competicao->observacoes;
            $competicao->is_publico = isset($dados['isPublico']) ? $dados['isPublico'] : $competicao->is_publico;
            $competicao->save();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Se temos ids de imagens a manter, excluímos as que não estão na lista
            if (!empty($imagensExistentes)) {
                // Excluímos fisicamente e do banco as imagens que não estão na lista de existentes
                $imagensParaExcluir = CompeticoesImagens::where('competicao_id', $competicao->id)
                                      ->whereNotIn('id', $imagensExistentes)
                                      ->get();
                
                $diretorio = __DIR__ . '/../admin/assets/imagens/arquivos/competicoes/';
                
                foreach ($imagensParaExcluir as $imagem) {
                    if (file_exists($diretorio . $imagem->url)) {
                        unlink($diretorio . $imagem->url);
                    }
                    $imagem->delete();
                }
            }
            
            // Processa e adiciona as novas imagens, se houver
            if (!empty($imagens)) {
                foreach ($imagens as $nomeImagem) {
                    // Adiciona a imagem ao banco
                    CompeticoesImagens::create([
                        'competicao_id' => $competicao->id,
                        'url' => $nomeImagem
                    ]);
                }
            }
            
            // Obtém todas as imagens da competição (existentes e novas)
            $imagens = CompeticoesImagens::where('competicao_id', $competicao->id)->get();
            $imagensUrls = [];
            
            // Formata URLs das imagens
            foreach ($imagens as $imagem) {
                $imagensUrls[] = [
                    'id' => $imagem->id,
                    'url' => $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $imagem->url
                ];
            }
            
            // Formata a resposta
            return [
                'status' => 'success',
                'message' => 'Competição atualizada com sucesso',
                'data' => [
                    'id' => $competicao->id,
                    'nomeEvento' => $competicao->nome_evento,
                    'cidade' => $competicao->cidade,
                    'data' => $competicao->data,
                    'modalidade' => $competicao->modalidade,
                    'colocacao' => $competicao->colocacao,
                    'numeroLutas' => (int)$competicao->numero_lutas,
                    'numeroVitorias' => (int)$competicao->numero_vitorias,
                    'numeroDerrotas' => (int)$competicao->numero_derrotas,
                    'numeroFinalizacoes' => (int)$competicao->numero_finalizacoes,
                    'observacoes' => $competicao->observacoes,
                    'isPublico' => (bool)$competicao->is_publico,
                    'imagens' => $imagensUrls
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar competição: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao atualizar competição',
                'errorCode' => 500,
                'data' => null
            ];
        }
    }
    
    /**
     * Exclui uma competição
     * Verifica se a competição pertence ao usuário autenticado
     * 
     * @param int $id ID da competição a ser excluída
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da operação
     */
    public static function excluir($id, $userId) {
        try {
            // Busca a competição com verificação de propriedade
            $competicao = Competicoes::where('id', $id)
                          ->where('user_id', $userId)
                          ->first();

            if (!$competicao) {
                return [
                    'status' => 'error',
                    'message' => 'Competição não encontrada ou você não tem permissão para excluí-la',
                    'errorCode' => 404,
                    'data' => null
                ];
            }

            // busca as imagens associadas à competição
            $imagens = CompeticoesImagens::where('competicao_id', $competicao->id)->get();
            $diretorio = __DIR__ . '/../admin/assets/imagens/arquivos/competicoes/';

            // exclui os arquivos físicos das imagens
            foreach ($imagens as $imagem) {
                if (file_exists($diretorio . $imagem->url)) {
                    unlink($diretorio . $imagem->url); // Remove o arquivo físico
                }
            }
            
            // Exclui a competição (as imagens serão excluídas automaticamente pelo ON DELETE CASCADE)
            $competicao->delete();
            
            // Formata a resposta
            return [
                'status' => 'success',
                'message' => 'Competição excluída com sucesso'
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao excluir competição: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao excluir competição',
                'errorCode' => 500,
                'data' => null
            ];
        }
    }
    
    /**
     * Altera a visibilidade de uma competição
     * Verifica se a competição pertence ao usuário autenticado
     * 
     * @param int $id ID da competição
     * @param bool $isPublico Nova visibilidade da competição
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da operação
     */
    public static function alterarVisibilidade($id, $userId, $isPublico) {
        try {
            // Busca a competição com verificação de propriedade
            $competicao = Competicoes::where('id', $id)
                          ->where('user_id', $userId)
                          ->first();
            
            if (!$competicao) {
                return [
                    'status' => 'error',
                    'message' => 'Competição não encontrada ou você não tem permissão para modificá-la',
                    'errorCode' => 404,
                    'data' => null
                ];
            }
            
            // Atualiza a visibilidade
            $competicao->is_publico = $isPublico;
            $competicao->save();
            
            // Formata a resposta
            return [
                'status' => 'success',
                'message' => 'Visibilidade da competição alterada com sucesso',
                'data' => [
                    'id' => $competicao->id,
                    'isPublico' => (bool)$competicao->is_publico
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao alterar visibilidade da competição: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao alterar visibilidade da competição',
                'errorCode' => 500,
                'data' => null
            ];
        }
    }
    
    /**
     * Remove uma imagem específica de uma competição
     * Verifica se a competição pertence ao usuário autenticado
     * 
     * @param int $competicaoId ID da competição
     * @param int $userId ID do usuário autenticado
     * @param int $imagemId ID da imagem a ser removida
     * @return array Resultado da operação
     */
    public static function removerImagem($competicaoId, $userId, $imagemId) {
        try {
            // Busca a competição com verificação de propriedade
            $competicao = Competicoes::where('id', $competicaoId)
                          ->where('user_id', $userId)
                          ->first();
            
            if (!$competicao) {
                return [
                    'status' => 'error',
                    'message' => 'Competição não encontrada ou você não tem permissão para modificá-la',
                    'errorCode' => 404,
                    'data' => null
                ];
            }
            
            // Verifica se a imagem pertence a esta competição
            $imagem = CompeticoesImagens::where('id', $imagemId)
                                 ->where('competicao_id', $competicaoId)
                                 ->first();
            
            if (!$imagem) {
                return [
                    'status' => 'error',
                    'message' => 'Imagem não encontrada ou não pertence a esta competição',
                    'errorCode' => 404,
                    'data' => null
                ];
            }
            
            // Exclui o arquivo físico
            $diretorio = __DIR__ . '/../admin/assets/imagens/arquivos/competicoes/';

            if (file_exists($diretorio . $imagem->url)) {
                unlink($diretorio . $imagem->url); // Remove o arquivo físico
            }
            $imagem->delete(); // Remove do banco de dados
            
            // Obtém as imagens restantes
            $imagensRestantes = CompeticoesImagens::where('competicao_id', $competicaoId)->get();
            $baseUrl = $_ENV['BASE_URL'];
            $imagensFormatadas = [];
            
            foreach ($imagensRestantes as $img) {
                $imagensFormatadas[] = [
                    'id' => $img->id,
                    'url' => $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $img->url
                ];
            }

            // Formata a resposta
            return [
                'status' => 'success',
                'message' => 'Imagem removida com sucesso',
                'data' => [
                    'competicaoId' => $competicaoId,
                    'imagensRestantes' => $imagensFormatadas
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao remover imagem da competição: ' . $th->getMessage(), 'ERROR');
            return [
                'status' => 'error',
                'message' => 'Erro ao remover imagem da competição',
                'errorCode' => 500,
                'data' => null
            ];
        }
    }
}
