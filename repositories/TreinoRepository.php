<?php

namespace Repositories;

use Models\Treino;
use Models\TreinoImagem;
use Core\Logger;

class TreinoRepository {
    
    /**
     * Obtém todos os treinos do usuário com filtros e paginação
     * 
     * @param int $userId ID do usuário autenticado
     * @param array $filtros Filtros a serem aplicados (tipo, diaSemana)
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Treinos filtrados e informações de paginação
     */
    public static function listar($userId, $filtros = [], $pagina = 1, $limite = 20) {
        try {
            // Inicia a consulta básica para os treinos do usuário
            $query = Treino::where('usuario_id', $userId);
            
            // Aplica filtro por tipo se especificado e não for "todos"
            if (isset($filtros['tipo']) && $filtros['tipo'] !== 'todos') {
                $query->where('tipo', $filtros['tipo']);
            }
            
            // Aplica filtro por dia da semana se especificado e não for "todos"
            if (isset($filtros['diaSemana']) && $filtros['diaSemana'] !== 'todos') {
                $query->where('dia_semana', $filtros['diaSemana']);
            }
            
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
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata os treinos para retorno
            $treinosFormatados = [];
            foreach ($treinos as $treino) {
                $imagensUrls = [];
                
                // Formata URLs das imagens com ID
                foreach ($treino->imagens as $imagem) {
                    $imagensUrls[] = [
                        'id' => $imagem->id,
                        'url' => $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $imagem->url
                    ];
                }
                
                // Informações do usuário
                $usuario = [
                    'nome' => $treino->usuario->nome,
                    'imagem' => $treino->usuario->imagem,
                    'faixa' => $treino->usuario->faixa
                ];
                
                $treinosFormatados[] = [
                    'id' => $treino->id,
                    'numeroAula' => $treino->numero_aula,
                    'tipo' => $treino->tipo,
                    'diaSemana' => $treino->dia_semana,
                    'horario' => $treino->horario,
                    'data' => $treino->data,
                    'observacoes' => $treino->observacoes,
                    'isPublico' => (bool)$treino->is_publico,
                    'imagens' => $imagensUrls,
                    'usuario' => $usuario
                ];
            }
            
            // Monta a resposta com os treinos e informações de paginação
            return [
                'success' => true,
                'message' => 'Treinos obtidos com sucesso',
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
            Logger::log('Erro ao listar treinos: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar treinos',
                'data' => [
                    'treinos' => [],
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
     * Obtém os treinos públicos da comunidade com paginação
     * 
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Treinos públicos e informações de paginação
     */
    public static function listarComunidade($pagina = 1, $limite = 20) {
        try {
            // Inicia a consulta para treinos públicos
            $query = Treino::where('is_publico', true);
            
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
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata os treinos para retorno
            $treinosFormatados = [];
            foreach ($treinos as $treino) {
                $imagensUrls = [];
                
                // Formata URLs das imagens
                foreach ($treino->imagens as $imagem) {
                    $imagensUrls[] = [
                        'id' => $imagem->id,
                        'url' => $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $imagem->url
                    ];
                }

                // montando a url da imagem do usuário
                $perfil_imagem = null;
                if ($treino->usuario->imagem) {
                    $perfil_imagem = $baseUrl . 'admin/assets/imagens/arquivos/perfil/' . $treino->usuario->imagem;
                }
                
                // Informações do usuário
                $usuario = [
                    'bjj_id' => $treino->usuario->bjj_id,
                    'nome' => $treino->usuario->nome,
                    'imagem' => $perfil_imagem,
                    'faixa' => $treino->usuario->faixa
                ];
                
                $treinosFormatados[] = [
                    'id' => $treino->id,
                    'numeroAula' => $treino->numero_aula,
                    'tipo' => $treino->tipo,
                    'diaSemana' => $treino->dia_semana,
                    'horario' => $treino->horario,
                    'data' => $treino->data,
                    'observacoes' => $treino->observacoes,
                    'isPublico' => (bool)$treino->is_publico,
                    'imagens' => $imagensUrls,
                    'usuario' => $usuario
                ];
            }
            
            // Monta a resposta com os treinos e informações de paginação
            return [
                'success' => true,
                'message' => 'Treinos da comunidade obtidos com sucesso',
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
            Logger::log('Erro ao listar treinos da comunidade: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar treinos da comunidade',
                'data' => [
                    'treinos' => [],
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
     * Cria um novo treino
     * 
     * @param array $dados Dados do treino (numeroAula, tipo, diaSemana, horario, data, observacoes, isPublico)
     * @param int $userId ID do usuário autenticado
     * @param array $imagens Array com nomes das imagens (opcional)
     * @return array Treino criado ou mensagem de erro
     */
    public static function criar($dados, $userId, $imagens = []) {
        try {
            // Validação básica dos dados
            if (empty($dados['numeroAula']) || empty($dados['tipo']) || empty($dados['diaSemana']) || 
                empty($dados['horario']) || empty($dados['data'])) {
                return [
                    'success' => false,
                    'message' => 'Todos os campos obrigatórios devem ser preenchidos',
                    'data' => null
                ];
            }
            
            // Cria o novo treino
            $treino = Treino::create([
                'usuario_id' => $userId,
                'numero_aula' => $dados['numeroAula'],
                'tipo' => $dados['tipo'],
                'dia_semana' => $dados['diaSemana'],
                'horario' => $dados['horario'],
                'data' => $dados['data'],
                'observacoes' => $dados['observacoes'] ?? '',
                'is_publico' => isset($dados['isPublico']) ? $dados['isPublico'] : false
            ]);
            
            // Processa e adiciona as imagens, se houver
            $imagensUrls = [];
            $baseUrl = $_ENV['BASE_URL'];
            
            if (!empty($imagens)) {
                foreach ($imagens as $nomeImagem) {
                    // Adiciona a imagem ao banco
                    TreinoImagem::create([
                        'treino_id' => $treino->id,
                        'url' => $nomeImagem
                    ]);
                    
                    // Adiciona a URL formatada ao array de resposta
                    $imagensUrls[] = $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $nomeImagem;
                }
            }
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Treino criado com sucesso',
                'data' => [
                    'id' => $treino->id,
                    'numeroAula' => $treino->numero_aula,
                    'tipo' => $treino->tipo,
                    'diaSemana' => $treino->dia_semana,
                    'horario' => $treino->horario,
                    'data' => $treino->data,
                    'observacoes' => $treino->observacoes,
                    'isPublico' => (bool)$treino->is_publico,
                    'imagens' => $imagensUrls
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao criar treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao criar treino',
                'data' => null
            ];
        }
    }
    
    /**
     * Atualiza um treino existente
     * Verifica se o treino pertence ao usuário autenticado
     * 
     * @param array $dados Dados atualizados do treino
     * @param int $userId ID do usuário autenticado
     * @param array $imagens Array com nomes das novas imagens (opcional)
     * @return array Treino atualizado ou mensagem de erro
     */
    public static function atualizar($dados, $userId, $imagens = []) {
        try {
            // Validação básica dos dados
            if (empty($dados['id']) || empty($dados['numeroAula']) || empty($dados['tipo']) || 
                empty($dados['diaSemana']) || empty($dados['horario']) || empty($dados['data'])) {
                return [
                    'success' => false,
                    'message' => 'Todos os campos obrigatórios devem ser preenchidos',
                    'data' => null
                ];
            }
            
            // Busca o treino com verificação de propriedade
            $treino = Treino::where('id', $dados['id'])
                          ->where('usuario_id', $userId)
                          ->first();
            
            if (!$treino) {
                return [
                    'success' => false,
                    'message' => 'Treino não encontrado ou você não tem permissão para editá-lo',
                    'data' => null
                ];
            }
            
            // Atualiza os dados
            $treino->numero_aula = $dados['numeroAula'];
            $treino->tipo = $dados['tipo'];
            $treino->dia_semana = $dados['diaSemana'];
            $treino->horario = $dados['horario'];
            $treino->data = $dados['data'];
            $treino->observacoes = $dados['observacoes'] ?? '';
            $treino->is_publico = isset($dados['isPublico']) ? $dados['isPublico'] : $treino->is_publico;
            $treino->save();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Processa e adiciona as novas imagens, se houver
            if (!empty($imagens)) {
                foreach ($imagens as $nomeImagem) {
                    // Adiciona a imagem ao banco
                    TreinoImagem::create([
                        'treino_id' => $treino->id,
                        'url' => $nomeImagem
                    ]);
                }
            }
            
            // Obtém todas as imagens do treino (existentes e novas)
            $imagens = TreinoImagem::where('treino_id', $treino->id)->get();
            $imagensUrls = [];
            
            // Formata URLs das imagens
            foreach ($imagens as $imagem) {
                $imagensUrls[] = $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $imagem->url;
            }
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Treino atualizado com sucesso',
                'data' => [
                    'id' => $treino->id,
                    'numeroAula' => $treino->numero_aula,
                    'tipo' => $treino->tipo,
                    'diaSemana' => $treino->dia_semana,
                    'horario' => $treino->horario,
                    'data' => $treino->data,
                    'observacoes' => $treino->observacoes,
                    'isPublico' => (bool)$treino->is_publico,
                    'imagens' => $imagensUrls
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao atualizar treino',
                'data' => null
            ];
        }
    }
    
    /**
     * Exclui um treino
     * Verifica se o treino pertence ao usuário autenticado
     * 
     * @param int $id ID do treino a ser excluído
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da operação
     */
    public static function excluir($id, $userId) {
        try {
            // Busca o treino com verificação de propriedade
            $treino = Treino::where('id', $id)
                          ->where('usuario_id', $userId)
                          ->first();

            // busca as imagens associadas ao treino
            $imagens = TreinoImagem::where('treino_id', $treino->id)->get();
            $diretorio = __DIR__ . '/../admin/assets/imagens/arquivos/treinos/';

            // exclui os arquivos físicos das imagens
            foreach ($imagens as $imagem) {
                if (file_exists($diretorio . $imagem->url)) {
                    unlink($diretorio . $imagem->url); // Remove o arquivo físico
                }
            }

            if (!$treino) {
                return [
                    'success' => false,
                    'message' => 'Treino não encontrado ou você não tem permissão para excluí-lo',
                    'data' => null
                ];
            }
            
            // Exclui o treino (as imagens serão excluídas automaticamente pelo ON DELETE CASCADE)
            $treino->delete();
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Treino excluído com sucesso',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao excluir treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao excluir treino',
                'data' => null
            ];
        }
    }
    
    /**
     * Altera a visibilidade de um treino
     * Verifica se o treino pertence ao usuário autenticado
     * 
     * @param int $id ID do treino
     * @param bool $isPublico Nova visibilidade do treino
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da operação
     */
    public static function alterarVisibilidade($id, $userId, $isPublico) {
        try {
            // Busca o treino com verificação de propriedade
            $treino = Treino::where('id', $id)
                          ->where('usuario_id', $userId)
                          ->first();
            
            if (!$treino) {
                return [
                    'success' => false,
                    'message' => 'Treino não encontrado ou você não tem permissão para modificá-lo',
                    'data' => null
                ];
            }
            
            // Atualiza a visibilidade
            $treino->is_publico = $isPublico;
            $treino->save();
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Visibilidade do treino alterada com sucesso',
                'data' => [
                    'id' => $treino->id,
                    'isPublico' => (bool)$treino->is_publico
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao alterar visibilidade do treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao alterar visibilidade do treino',
                'data' => null
            ];
        }
    }
    
    /**
     * Adiciona imagens a um treino
     * Verifica se o treino pertence ao usuário autenticado
     * 
     * @param int $treinoId ID do treino
     * @param int $userId ID do usuário autenticado
     * @param array $imagens Array com nomes das imagens
     * @return array Resultado da operação com URLs das imagens
     */
    public static function adicionarImagens($treinoId, $userId, $imagens) {
        try {
            // Busca o treino com verificação de propriedade
            $treino = Treino::where('id', $treinoId)
                          ->where('usuario_id', $userId)
                          ->first();
            
            if (!$treino) {
                return [
                    'success' => false,
                    'message' => 'Treino não encontrado ou você não tem permissão para modificá-lo',
                    'data' => null
                ];
            }
            
            $imagensAdicionadas = [];
            $baseUrl = $_ENV['BASE_URL'];
            
            // Adiciona cada imagem ao banco
            foreach ($imagens as $nomeImagem) {
                $imagem = TreinoImagem::create([
                    'treino_id' => $treino->id,
                    'url' => $nomeImagem
                ]);
                
                $imagensAdicionadas[] = $baseUrl . 'admin/assets/imagens/arquivos/treinos/' . $nomeImagem;
            }
            
            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Imagens carregadas com sucesso',
                'data' => [
                    'imagens' => $imagensAdicionadas
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao adicionar imagens ao treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao adicionar imagens ao treino',
                'data' => null
            ];
        }
    }
    
    /**
     * Remove uma imagem específica de um treino
     * Verifica se o treino pertence ao usuário autenticado
     * 
     * @param int $treinoId ID do treino
     * @param int $userId ID do usuário autenticado
     * @param int $imagemId ID da imagem a ser removida
     * @return array Resultado da operação
     */
    public static function removerImagem($treinoId, $userId, $imagemId) {
        try {
            // Busca o treino com verificação de propriedade
            $treino = Treino::where('id', $treinoId)
                          ->where('usuario_id', $userId)
                          ->first();
            
            if (!$treino) {
                return [
                    'success' => false,
                    'message' => 'Treino não encontrado ou você não tem permissão para modificá-lo',
                    'data' => null
                ];
            }
            
            // Verifica se a imagem pertence a este treino
            $imagem = TreinoImagem::where('id', $imagemId)
                                 ->where('treino_id', $treinoId)
                                 ->first();
            
            if (!$imagem) {
                return [
                    'success' => false,
                    'message' => 'Imagem não encontrada ou não pertence a este treino',
                    'data' => null
                ];
            }
            
            // Exclui o arquivo físico
            $diretorio = __DIR__ . '/../admin/assets/imagens/arquivos/treinos/';

            if (file_exists($diretorio . $imagem->url)) {
                unlink($diretorio . $imagem->url); // Remove o arquivo físico
            }
            $imagem->delete(); // Remove do banco de dados

            // Formata a resposta
            return [
                'success' => true,
                'message' => 'Imagem removida com sucesso',
                'data' => [
                    'imagem_removida' => $imagemId
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao remover imagem do treino: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao remover imagem do treino',
                'data' => null
            ];
        }
    }
}
