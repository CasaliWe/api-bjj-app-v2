<?php

namespace Repositories;

use Models\User;
use Models\Treino;
use Models\Tecnicas;
use Models\Competicoes;
use Core\Logger;

/**
 * Métodos para endpoints públicos de perfil de usuário
 */
class UserPerfilRepository {
    
    /**
     * Obtém o perfil público de um usuário pelo bjj_id
     * 
     * @param string $bjjId O ID BJJ do usuário
     * @return array|null Dados do perfil ou null se não encontrado
     */
    public static function getProfile($bjjId) {
        try {
            // Busca o usuário pelo bjj_id
            $usuario = User::where('bjj_id', $bjjId)->first();
            
            if (!$usuario) {
                return null;
            }
            
            // Formata o resultado conforme esperado
            return [
                'id' => $usuario->id,
                'bjj_id' => $usuario->bjj_id,
                'nome' => $usuario->nome,
                'email' => $usuario->email,
                'imagem' => $usuario->imagem,
                'whatsapp' => $usuario->whatsapp,
                'idade' => $usuario->idade,
                'peso' => $usuario->peso,
                'faixa' => $usuario->faixa,
                'competidor' => $usuario->competidor,
                'estilo' => $usuario->estilo,
                'finalizacao' => $usuario->finalizacao,
                'academia' => $usuario->academia,
                'cidade' => $usuario->cidade,
                'estado' => $usuario->estado,
                'pais' => $usuario->pais,
                'instagram' => $usuario->instagram,
                'youtube' => $usuario->youtube,
                'tiktok' => $usuario->tiktok,
                'bio' => $usuario->bio,
                'perfil_publico' => $usuario->perfilPublico,
                'exp' => $usuario->exp,
                'plano' => $usuario->plano,
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao obter perfil do usuário: ' . $th->getMessage(), 'ERROR');
            return null;
        }
    }
    
    /**
     * Obtém os treinos públicos de um usuário
     * 
     * @param string $bjjId O ID BJJ do usuário
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Treinos públicos e informações de paginação
     */
    public static function getPublicTrainings($bjjId, $pagina = 1, $limite = 10) {
        try {
            // Busca o ID do usuário pelo bjj_id
            $usuario = User::where('bjj_id', $bjjId)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ];
            }
            
            // Inicia a consulta para treinos públicos do usuário
            $query = Treino::where('usuario_id', $usuario->id)
                         ->where('is_publico', true);
            
            // Obtém o total de itens para a paginação
            $totalItems = $query->count();
            
            // Calcula o total de páginas
            $totalPages = ceil($totalItems / $limite);
            
            // Ajusta a página atual se necessário
            $pagina = max(1, min($pagina, $totalPages ?: 1));
            
            // Calcula o offset para a consulta
            $offset = ($pagina - 1) * $limite;
            
            // Obtém os treinos com paginação, ordenados por data de criação mais recente
            $treinos = $query->with(['imagens', 'usuario'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '';
            
            // Formata os treinos para retorno
            $treinosFormatados = [];
            foreach ($treinos as $treino) {
                $imagensUrls = [];
                
                // Formata URLs das imagens
                foreach ($treino->imagens as $imagem) {
                    $imagensUrls[] = $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $imagem->url;
                }
                
                // Informações do usuário
                $usuario = [
                    'nome' => $treino->usuario->nome,
                    'imagem' => $treino->usuario->imagem,
                    'faixa' => $treino->usuario->faixa
                ];
                
                $treinosFormatados[] = [
                    'id' => $treino->id,
                    'tipo' => $treino->tipo,
                    'diaSemana' => $treino->dia_semana,
                    'horario' => $treino->horario,
                    'numeroAula' => $treino->numero_aula,
                    'data' => $treino->data,
                    'imagens' => $imagensUrls,
                    'observacoes' => $treino->observacoes,
                    'isPublico' => (bool)$treino->is_publico,
                    'usuario' => $usuario
                ];
            }
            
            // Monta a resposta com os treinos e informações de paginação
            return [
                'success' => true,
                'message' => 'Treinos públicos obtidos com sucesso',
                'data' => [
                    'treinos' => $treinosFormatados,
                    'pagination' => [
                        'currentPage' => (int)$pagina,
                        'totalPages' => (int)$totalPages,
                        'totalItems' => (int)$totalItems,
                        'itemsPerPage' => (int)$limite
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao obter treinos públicos: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao obter treinos públicos'
            ];
        }
    }
    
    /**
     * Obtém as competições públicas de um usuário
     * 
     * @param string $bjjId O ID BJJ do usuário
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Competições públicas e informações de paginação
     */
    public static function getPublicCompetitions($bjjId, $pagina = 1, $limite = 10) {
        try {
            // Busca o ID do usuário pelo bjj_id
            $usuario = User::where('bjj_id', $bjjId)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ];
            }
            
            // Inicia a consulta para competições públicas do usuário
            $query = Competicoes::where('user_id', $usuario->id)
                              ->where('is_publico', true);
            
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
            $baseUrl = isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '';
            
            // Formata as competições para retorno
            $competicoesFormatadas = [];
            foreach ($competicoes as $competicao) {
                $imagensUrls = [];
                
                // Formata URLs das imagens
                foreach ($competicao->imagens as $imagem) {
                    $imagensUrls[] = $baseUrl . 'admin/assets/imagens/arquivos/competicoes/' . $imagem->url;
                }
                
                // Informações do usuário
                $user = [
                    'nome' => $competicao->user->nome,
                    'imagem' => $competicao->user->imagem,
                    'faixa' => $competicao->user->faixa
                ];
                
                $competicoesFormatadas[] = [
                    'id' => $competicao->id,
                    'nome' => $competicao->nome_evento,
                    'data' => $competicao->data,
                    'local' => $competicao->cidade,
                    'modalidade' => $competicao->modalidade,
                    'resultado' => $competicao->colocacao,
                    'imagens' => $imagensUrls,
                    'numero_lutas' => $competicao->numero_lutas,
                    'numero_vitorias' => $competicao->numero_vitorias,
                    'numero_derrotas' => $competicao->numero_derrotas,
                    'numero_finalizacoes' => $competicao->numero_finalizacoes,
                    'usuario' => $user
                ];
            }
            
            // Monta a resposta com as competições e informações de paginação
            return [
                'success' => true,
                'message' => 'Competições públicas obtidas com sucesso',
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
            Logger::log('Erro ao obter competições públicas: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao obter competições públicas'
            ];
        }
    }
    
    /**
     * Obtém as técnicas públicas de um usuário
     * 
     * @param string $bjjId O ID BJJ do usuário
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Técnicas públicas e informações de paginação
     */
    public static function getPublicTechniques($bjjId, $pagina = 1, $limite = 10) {
        try {
            // Busca o ID do usuário pelo bjj_id
            $usuario = User::where('bjj_id', $bjjId)->first();
            
            if (!$usuario) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ];
            }
            
            // Inicia a consulta para técnicas públicas do usuário
            $query = Tecnicas::where('usuario_id', $usuario->id)
                          ->where('publica', true);
            
            // Obtém o total de itens para a paginação
            $totalItems = $query->count();
            
            // Calcula o total de páginas
            $totalPages = ceil($totalItems / $limite);
            
            // Ajusta a página atual se necessário
            $pagina = max(1, min($pagina, $totalPages ?: 1));
            
            // Calcula o offset para a consulta
            $offset = ($pagina - 1) * $limite;
            
            // Obtém as técnicas com paginação, ordenadas por data de criação mais recente
            $tecnicas = $query->with(['usuario'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = isset($_ENV['BASE_URL']) ? $_ENV['BASE_URL'] : '';
            
            // Formata as técnicas para retorno
            $tecnicasFormatadas = [];
            foreach ($tecnicas as $tecnica) {
                // Informações do usuário
                $usuario = [
                    'nome' => $tecnica->usuario->nome,
                    'imagem' => $tecnica->usuario->imagem,
                    'faixa' => $tecnica->usuario->faixa
                ];
                
                // Deserializa arrays armazenados como json ou string
                $passos = is_string($tecnica->passos) ? json_decode($tecnica->passos, true) : $tecnica->passos;
                $observacoes = is_string($tecnica->observacoes) ? json_decode($tecnica->observacoes, true) : $tecnica->observacoes;
                
                if (!is_array($passos)) $passos = [];
                if (!is_array($observacoes)) $observacoes = [];
                
                $tecnicasFormatadas[] = [
                    'id' => $tecnica->id,
                    'nome' => $tecnica->nome,
                    'categoria' => $tecnica->categoria,
                    'posicao' => $tecnica->posicao,
                    'passos' => $passos,
                    'observacoes' => $observacoes,
                    'nota' => $tecnica->nota,
                    'video' => $tecnica->video,
                    'video_url' => $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/videos/' . $tecnica->video_url,
                    'video_poster' => $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/posters/' . $tecnica->video_poster,
                    'destacado' => (bool)$tecnica->destacado,
                    'publica' => (bool)$tecnica->publica,
                    'criado_em' => $tecnica->created_at,
                    'atualizado_em' => $tecnica->updated_at,
                    'usuario' => $usuario
                ];
            }
            
            // Monta a resposta com as técnicas e informações de paginação
            return [
                'success' => true,
                'message' => 'Técnicas públicas obtidas com sucesso',
                'data' => [
                    'tecnicas' => $tecnicasFormatadas,
                    'pagination' => [
                        'currentPage' => (int)$pagina,
                        'totalPages' => (int)$totalPages,
                        'totalItems' => (int)$totalItems,
                        'itemsPerPage' => (int)$limite
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao obter técnicas públicas: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao obter técnicas públicas'
            ];
        }
    }
}