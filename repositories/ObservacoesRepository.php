<?php

namespace Repositories;

use Models\Observacoes;
use Core\Logger;

class ObservacoesRepository {
    /**
     * Obtém todas as observações do usuário com filtros e paginação
     * 
     * @param int $userId ID do usuário autenticado
     * @param array $filtros Filtros a serem aplicados (tag, termo)
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Observações filtradas e informações de paginação
     */
    public static function listar($userId, $filtros = [], $pagina = 1, $limite = 12) {
        try {
            // Inicia a consulta básica para as observações do usuário
            $query = Observacoes::where('usuario_id', $userId);
            
            // Aplica filtro por tag se especificado e não for "todas"
            if (isset($filtros['tag']) && $filtros['tag'] !== 'todas') {
                $query->where('tag', $filtros['tag']);
            }
            
            // Aplica filtro por termo de busca se especificado
            if (isset($filtros['termo']) && !empty($filtros['termo'])) {
                $termo = $filtros['termo'];
                $query->where(function($q) use ($termo) {
                    $q->where('titulo', 'LIKE', "%{$termo}%")
                      ->orWhere('conteudo', 'LIKE', "%{$termo}%");
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
            
            // Obtém as observações com paginação, ordenadas por data mais recente
            $observacoes = $query->orderBy('data', 'desc')
                                ->offset($offset)
                                ->limit($limite)
                                ->get()
                                ->toArray();
            
            // Formata as observações para retorno (renomeando usuario_id para usuarioId)
            $observacoesFormatadas = array_map(function($obs) {
                return [
                    'id' => $obs['id'],
                    'titulo' => $obs['titulo'],
                    'conteudo' => $obs['conteudo'],
                    'tag' => $obs['tag'],
                    'data' => $obs['data'],
                    'usuarioId' => $obs['usuario_id']
                ];
            }, $observacoes);
            
            // Monta a resposta com as observações e informações de paginação
            return [
                'success' => true,
                'message' => 'Observações obtidas com sucesso',
                'data' => [
                    'observacoes' => $observacoesFormatadas,
                    'paginacao' => [
                        'currentPage' => (int)$pagina,
                        'totalPages' => (int)$totalPages,
                        'totalItems' => (int)$totalItems
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao listar observações: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar observações',
                'data' => [
                    'observacoes' => [],
                    'paginacao' => [
                        'currentPage' => 1,
                        'totalPages' => 0,
                        'totalItems' => 0
                    ]
                ]
            ];
        }
    }
    
    /**
     * Obtém uma observação específica pelo ID
     * Verifica se a observação pertence ao usuário autenticado
     * 
     * @param int $id ID da observação
     * @param int $userId ID do usuário autenticado
     * @return array Observação encontrada ou mensagem de erro
     */
    public static function obter($id, $userId) {
        try {
            // Busca a observação com verificação de propriedade
            $observacao = Observacoes::where('id', $id)
                                    ->where('usuario_id', $userId)
                                    ->first();
            
            if (!$observacao) {
                return [
                    'success' => false,
                    'message' => 'Observação não encontrada ou você não tem permissão para visualizá-la',
                    'data' => null
                ];
            }
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Observação obtida com sucesso',
                'data' => [
                    'id' => $observacao->id,
                    'titulo' => $observacao->titulo,
                    'conteudo' => $observacao->conteudo,
                    'tag' => $observacao->tag,
                    'data' => $observacao->data,
                    'usuarioId' => $observacao->usuario_id
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao obter observação: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao obter observação',
                'data' => null
            ];
        }
    }
    
    /**
     * Adiciona uma nova observação
     * 
     * @param array $dados Dados da observação (titulo, conteudo, tag)
     * @param int $userId ID do usuário autenticado
     * @return array Observação criada ou mensagem de erro
     */
    public static function adicionar($dados, $userId) {
        try {            
            // Cria a nova observação
            $observacao = Observacoes::create([
                'titulo' => $dados['titulo'],
                'conteudo' => $dados['conteudo'],
                'tag' => $dados['tag'],
                'data' => date('Y-m-d'), // Apenas data, sem horário
                'usuario_id' => $userId
            ]);
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Observação adicionada com sucesso',
                'data' => [
                    'id' => $observacao->id,
                    'titulo' => $observacao->titulo,
                    'conteudo' => $observacao->conteudo,
                    'tag' => $observacao->tag,
                    'data' => $observacao->data,
                    'usuarioId' => $observacao->usuario_id
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao adicionar observação: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao adicionar observação',
                'data' => null
            ];
        }
    }
    
    /**
     * Atualiza uma observação existente
     * Verifica se a observação pertence ao usuário autenticado
     * 
     * @param array $dados Dados atualizados da observação (id, titulo, conteudo, tag)
     * @param int $userId ID do usuário autenticado
     * @return array Observação atualizada ou mensagem de erro
     */
    public static function atualizar($dados, $userId) {
        try {            
            // Busca a observação com verificação de propriedade
            $observacao = Observacoes::where('id', $dados['id'])
                                     ->where('usuario_id', $userId)
                                     ->first();
            
            if (!$observacao) {
                return [
                    'success' => false,
                    'message' => 'Observação não encontrada ou você não tem permissão para editá-la',
                    'data' => null
                ];
            }
            
            // Atualiza os dados
            $observacao->titulo = $dados['titulo'];
            $observacao->conteudo = $dados['conteudo'];
            $observacao->tag = $dados['tag'];
            $observacao->data_atualizacao = date('Y-m-d'); // Apenas data, sem horário
            $observacao->save();
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Observação atualizada com sucesso',
                'data' => [
                    'id' => $observacao->id,
                    'titulo' => $observacao->titulo,
                    'conteudo' => $observacao->conteudo,
                    'tag' => $observacao->tag,
                    'data' => $observacao->data,
                    'usuarioId' => $observacao->usuario_id
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar observação: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao atualizar observação',
                'data' => null
            ];
        }
    }
    
    /**
     * Exclui uma observação
     * Verifica se a observação pertence ao usuário autenticado
     * 
     * @param int $id ID da observação a ser excluída
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da operação
     */
    public static function excluir($id, $userId) {
        try {
            // Busca a observação com verificação de propriedade
            $observacao = Observacoes::where('id', $id)
                                     ->where('usuario_id', $userId)
                                     ->first();
            
            if (!$observacao) {
                return [
                    'success' => false,
                    'message' => 'Observação não encontrada ou você não tem permissão para excluí-la',
                    'data' => null
                ];
            }
            
            // Exclui a observação
            $observacao->delete();
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Observação excluída com sucesso',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao excluir observação: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao excluir observação',
                'data' => null
            ];
        }
    }
}
