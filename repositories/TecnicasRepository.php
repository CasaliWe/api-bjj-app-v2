<?php

namespace Repositories;

use Models\Tecnicas;
use Models\Posicoes;
use Core\Logger;

class TecnicasRepository {
    
    /**
     * Obtém todas as técnicas do usuário com filtros e paginação
     * 
     * @param int $userId ID do usuário autenticado
     * @param array $filtros Filtros a serem aplicados (categoria, posicao)
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @return array Técnicas filtradas e informações de paginação
     */
    public static function listar($userId, $filtros = [], $pagina = 1, $limite = 20) {
        try {
            // Inicia a consulta básica para as técnicas do usuário
            $query = Tecnicas::where('usuario_id', $userId);
            
            // Aplica filtro por categoria se especificado
            if (isset($filtros['categoria']) && !empty($filtros['categoria'])) {
                $query->where('categoria', $filtros['categoria']);
            }
            
            // Aplica filtro por posição se especificado
            if (isset($filtros['posicao']) && !empty($filtros['posicao'])) {
                $query->where('posicao', $filtros['posicao']);
            }
            
            // Obtém o total de itens para a paginação
            $totalItems = $query->count();
            
            // Calcula o total de páginas
            $totalPages = ceil($totalItems / $limite);
            
            // Ajusta a página atual se necessário
            $pagina = max(1, min($pagina, $totalPages ?: 1));
            
            // Calcula o offset para a consulta
            $offset = ($pagina - 1) * $limite;
            
            // Obtém as técnicas com paginação, ordenados por data de criação mais recente
            $tecnicas = $query->with(['usuario', 'posicaoDetalhes'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata as técnicas para retorno
            $tecnicasFormatadas = [];
            foreach ($tecnicas as $tecnica) {
                // Formata os passos e observações (que são armazenados como JSON)
                $passos = json_decode($tecnica->passos, true) ?: [];
                $observacoes = json_decode($tecnica->observacoes, true) ?: [];
                
                // Monta URLs de vídeo e poster, se existirem
                $videoUrl = null;
                $videoPoster = null;
                
                if (!empty($tecnica->video_url)) {
                    $videoUrl = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/videos/' . $tecnica->video_url;
                }
                
                if (!empty($tecnica->video_poster)) {
                    $videoPoster = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/posters/' . $tecnica->video_poster;
                }
                
                $tecnicasFormatadas[] = [
                    'id' => $tecnica->id,
                    'nome' => $tecnica->nome,
                    'categoria' => $tecnica->categoria,
                    'posicao' => $tecnica->posicao,
                    'passos' => $passos,
                    'observacoes' => $observacoes,
                    'nota' => (int)$tecnica->nota,
                    'video' => $tecnica->video,
                    'video_url' => $videoUrl,
                    'video_poster' => $videoPoster,
                    'destacado' => (bool)$tecnica->destacado,
                    'publica' => (bool)$tecnica->publica,
                    'criado_em' => $tecnica->created_at->format('Y-m-d\TH:i:s'),
                    'atualizado_em' => $tecnica->updated_at->format('Y-m-d\TH:i:s')
                ];
            }
            
            // Monta a resposta com as técnicas e informações de paginação
            return [
                'success' => true,
                'message' => 'Técnicas listadas com sucesso',
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
            Logger::log('Erro ao listar técnicas: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar técnicas',
                'data' => [
                    'tecnicas' => [],
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
     * Obtém as técnicas públicas da comunidade com paginação e pesquisa
     * 
     * @param int $pagina Número da página atual
     * @param int $limite Número de itens por página
     * @param string $termo Termo para pesquisa (opcional)
     * @return array Técnicas públicas e informações de paginação
     */
    public static function listarComunidade($pagina = 1, $limite = 20, $termo = null) {
        try {
            // Inicia a consulta para técnicas públicas
            $query = Tecnicas::where('publica', true);
            
            // Aplica filtro de pesquisa se termo for fornecido
            if (!empty($termo)) {
                $query->where(function($q) use ($termo) {
                    $q->where('nome', 'like', "%{$termo}%")
                      ->orWhere('categoria', 'like', "%{$termo}%")
                      ->orWhere('posicao', 'like', "%{$termo}%");
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
            
            // Obtém as técnicas com paginação, ordenados por data de criação mais recente
            $tecnicas = $query->with(['usuario', 'posicaoDetalhes'])
                           ->orderBy('created_at', 'desc')
                           ->offset($offset)
                           ->limit($limite)
                           ->get();
            
            // Obter a URL base do .env
            $baseUrl = $_ENV['BASE_URL'];
            
            // Formata as técnicas para retorno
            $tecnicasFormatadas = [];
            foreach ($tecnicas as $tecnica) {
                // Formata os passos e observações (que são armazenados como JSON)
                $passos = json_decode($tecnica->passos, true) ?: [];
                $observacoes = json_decode($tecnica->observacoes, true) ?: [];
                
                // Monta URLs de vídeo e poster, se existirem
                $videoUrl = null;
                $videoPoster = null;
                
                if (!empty($tecnica->video_url)) {
                    $videoUrl = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/videos/' . $tecnica->video_url;
                }
                
                if (!empty($tecnica->video_poster)) {
                    $videoPoster = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/posters/' . $tecnica->video_poster;
                }
                
                // Informações do autor (usuário)
                $autor = [
                    'id' => $tecnica->usuario->id,
                    'nome' => $tecnica->usuario->nome,
                    'faixa' => $tecnica->usuario->faixa,
                    'imagem' => $baseUrl . 'admin/assets/imagens/arquivos/perfil/' . $tecnica->usuario->imagem,
                    'bjj_id' => $tecnica->usuario->bjj_id,
                ];
                
                $tecnicasFormatadas[] = [
                    'id' => $tecnica->id,
                    'nome' => $tecnica->nome,
                    'categoria' => $tecnica->categoria,
                    'posicao' => $tecnica->posicao,
                    'passos' => $passos,
                    'observacoes' => $observacoes,
                    'nota' => (int)$tecnica->nota,
                    'video' => $tecnica->video,
                    'video_url' => $videoUrl,
                    'video_poster' => $videoPoster,
                    'destacado' => (bool)$tecnica->destacado,
                    'publica' => (bool)$tecnica->publica,
                    'autor' => $autor,
                    'criado_em' => $tecnica->created_at->format('Y-m-d\TH:i:s'),
                    'atualizado_em' => $tecnica->updated_at->format('Y-m-d\TH:i:s')
                ];
            }
            
            // Monta a resposta com as técnicas da comunidade
            return [
                'success' => true,
                'message' => 'Técnicas da comunidade listadas com sucesso',
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
            Logger::log('Erro ao listar técnicas da comunidade: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar técnicas da comunidade',
                'data' => [
                    'tecnicas' => [],
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
     * Lista todas as posições disponíveis (padrão e do usuário)
     * 
     * @param int $userId ID do usuário autenticado
     * @return array Lista de posições
     */
    public static function listarPosicoes($userId) {
        try {
            // Busca posições padrão e do usuário
            $posicoes = Posicoes::where('padrao', true)
                              ->orWhere('usuario_id', $userId)
                              ->orderBy('nome', 'asc')
                              ->pluck('nome')
                              ->toArray();
            
            return [
                'success' => true,
                'message' => 'Posições listadas com sucesso',
                'data' => [
                    'posicoes' => $posicoes
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao listar posições: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao listar posições',
                'data' => [
                    'posicoes' => []
                ]
            ];
        }
    }
    
    /**
     * Cria uma nova técnica para o usuário
     * 
     * @param int $userId ID do usuário autenticado
     * @param array $dados Dados da técnica
     * @param array $videoData Informações do vídeo (arquivo, nome, etc)
     * @return array Resultado da criação
     */
    public static function criar($userId, $dados, $videoData = null) {
        try {
            // Verifica se a posição já existe, se não, cria
            self::verificarECriarPosicao($dados['posicao'], $userId);
            
            // Prepara os dados para criação
            $tecnicaData = [
                'usuario_id' => $userId,
                'nome' => $dados['nome'],
                'categoria' => $dados['categoria'],
                'posicao' => $dados['posicao'],
                'passos' => json_encode($dados['passos'] ?? []),
                'observacoes' => json_encode($dados['observacoes'] ?? []),
                'nota' => $dados['nota'] ?? 0,
                'destacado' => isset($dados['destacado']) ? (bool)$dados['destacado'] : false,
                'publica' => isset($dados['publica']) ? (bool)$dados['publica'] : false,
                'video' => $dados['video'] ?? null,
                'video_url' => null,
                'video_poster' => null
            ];
            
            // Processa o vídeo, se fornecido
            if ($videoData && isset($videoData['videoFile']) && $videoData['videoFile']['error'] === UPLOAD_ERR_OK) {
                // Salva os arquivos e obtém os nomes
                $arquivos = self::salvarArquivosVideo($videoData);
                if ($arquivos) {
                    $tecnicaData['video_url'] = $arquivos['video'];
                    $tecnicaData['video_poster'] = $arquivos['poster'];
                }
            }
            
            // Cria a técnica
            $tecnica = Tecnicas::create($tecnicaData);
            
            // Obtém a URL base
            $baseUrl = $_ENV['BASE_URL'];
            
            // Prepara os dados para retorno
            $videoUrl = null;
            $videoPoster = null;
            
            if (!empty($tecnica->video_url)) {
                $videoUrl = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/videos/' . $tecnica->video_url;
            }
            
            if (!empty($tecnica->video_poster)) {
                $videoPoster = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/posters/' . $tecnica->video_poster;
            }
            
            $tecnicaFormatada = [
                'id' => $tecnica->id,
                'nome' => $tecnica->nome,
                'categoria' => $tecnica->categoria,
                'posicao' => $tecnica->posicao,
                'passos' => json_decode($tecnica->passos, true) ?: [],
                'observacoes' => json_decode($tecnica->observacoes, true) ?: [],
                'nota' => (int)$tecnica->nota,
                'video' => $tecnica->video,
                'video_url' => $videoUrl,
                'video_poster' => $videoPoster,
                'destacado' => (bool)$tecnica->destacado,
                'publica' => (bool)$tecnica->publica,
                'criado_em' => $tecnica->created_at->format('Y-m-d\TH:i:s'),
                'atualizado_em' => $tecnica->updated_at->format('Y-m-d\TH:i:s')
            ];
            
            return [
                'success' => true,
                'message' => 'Técnica criada com sucesso',
                'data' => $tecnicaFormatada
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao criar técnica: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao criar técnica: ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Atualiza uma técnica existente
     * 
     * @param int $tecnicaId ID da técnica
     * @param int $userId ID do usuário autenticado
     * @param array $dados Dados da técnica
     * @param array $videoData Informações do vídeo (arquivo, nome, etc)
     * @return array Resultado da atualização
     */
    public static function atualizar($tecnicaId, $userId, $dados, $videoData = null) {
        try {
            // Busca a técnica
            $tecnica = Tecnicas::where('id', $tecnicaId)
                           ->where('usuario_id', $userId)
                           ->first();
            
            if (!$tecnica) {
                return [
                    'success' => false,
                    'message' => 'Técnica não encontrada ou não pertence ao usuário',
                    'data' => null
                ];
            }
            
            // Verifica se a posição já existe, se não, cria
            self::verificarECriarPosicao($dados['posicao'], $userId);
            
            // Prepara os dados para atualização
            $tecnicaData = [
                'nome' => $dados['nome'],
                'categoria' => $dados['categoria'],
                'posicao' => $dados['posicao'],
                'passos' => json_encode($dados['passos'] ?? []),
                'observacoes' => json_encode($dados['observacoes'] ?? []),
                'nota' => $dados['nota'] ?? $tecnica->nota,
                'destacado' => isset($dados['destacado']) ? (bool)$dados['destacado'] : $tecnica->destacado,
                'publica' => isset($dados['publica']) ? (bool)$dados['publica'] : $tecnica->publica,
                'video' => $dados['video'] ?? $tecnica->video
            ];
            
            // Processa o vídeo, se fornecido
            if ($videoData && isset($videoData['videoFile']) && $videoData['videoFile']['error'] === UPLOAD_ERR_OK) {
                // Remove arquivos antigos, se existirem
                self::removerArquivosVideo($tecnica->video_url, $tecnica->video_poster);
                
                // Salva os novos arquivos e obtém os nomes
                $arquivos = self::salvarArquivosVideo($videoData);
                if ($arquivos) {
                    $tecnicaData['video_url'] = $arquivos['video'];
                    $tecnicaData['video_poster'] = $arquivos['poster'];
                }
            }
            
            // Atualiza a técnica
            $tecnica->update($tecnicaData);
            $tecnica->refresh();
            
            // Obtém a URL base
            $baseUrl = $_ENV['BASE_URL'];
            
            // Prepara os dados para retorno
            $videoUrl = null;
            $videoPoster = null;
            
            if (!empty($tecnica->video_url)) {
                $videoUrl = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/videos/' . $tecnica->video_url;
            }
            
            if (!empty($tecnica->video_poster)) {
                $videoPoster = $baseUrl . 'admin/assets/imagens/arquivos/tecnicas/posters/' . $tecnica->video_poster;
            }
            
            $tecnicaFormatada = [
                'id' => $tecnica->id,
                'nome' => $tecnica->nome,
                'categoria' => $tecnica->categoria,
                'posicao' => $tecnica->posicao,
                'passos' => json_decode($tecnica->passos, true) ?: [],
                'observacoes' => json_decode($tecnica->observacoes, true) ?: [],
                'nota' => (int)$tecnica->nota,
                'video' => $tecnica->video,
                'video_url' => $videoUrl,
                'video_poster' => $videoPoster,
                'destacado' => (bool)$tecnica->destacado,
                'publica' => (bool)$tecnica->publica,
                'criado_em' => $tecnica->created_at->format('Y-m-d\TH:i:s'),
                'atualizado_em' => $tecnica->updated_at->format('Y-m-d\TH:i:s')
            ];
            
            return [
                'success' => true,
                'message' => 'Técnica atualizada com sucesso',
                'data' => $tecnicaFormatada
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar técnica: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao atualizar técnica: ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Exclui uma técnica
     * 
     * @param int $tecnicaId ID da técnica
     * @param int $userId ID do usuário autenticado
     * @return array Resultado da exclusão
     */
    public static function excluir($tecnicaId, $userId) {
        try {
            // Busca a técnica
            $tecnica = Tecnicas::where('id', $tecnicaId)
                           ->where('usuario_id', $userId)
                           ->first();
            
            if (!$tecnica) {
                return [
                    'success' => false,
                    'message' => 'Técnica não encontrada ou não pertence ao usuário',
                    'data' => null
                ];
            }
            
            // Remove arquivos, se existirem
            self::removerArquivosVideo($tecnica->video_url, $tecnica->video_poster);
            
            // Exclui a técnica
            $tecnica->delete();
            
            return [
                'success' => true,
                'message' => 'Técnica excluída com sucesso',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao excluir técnica: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao excluir técnica: ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Altera o status de destaque de uma técnica
     * 
     * @param int $tecnicaId ID da técnica
     * @param int $userId ID do usuário autenticado
     * @param bool $destacado Novo status de destaque
     * @return array Resultado da alteração
     */
    public static function alterarDestaque($tecnicaId, $userId, $destacado) {
        try {
            // Busca a técnica
            $tecnica = Tecnicas::where('id', $tecnicaId)
                           ->where('usuario_id', $userId)
                           ->first();
            
            if (!$tecnica) {
                return [
                    'success' => false,
                    'message' => 'Técnica não encontrada ou não pertence ao usuário',
                    'data' => null
                ];
            }
            
            // Atualiza o status de destaque
            $tecnica->destacado = $destacado;
            $tecnica->save();
            
            return [
                'success' => true,
                'message' => 'Destaque atualizado com sucesso',
                'data' => [
                    'id' => $tecnica->id,
                    'destacado' => (bool)$tecnica->destacado
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao alterar destaque: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao alterar destaque: ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Altera a visibilidade de uma técnica
     * 
     * @param int $tecnicaId ID da técnica
     * @param int $userId ID do usuário autenticado
     * @param bool $publica Novo status de visibilidade
     * @return array Resultado da alteração
     */
    public static function alterarVisibilidade($tecnicaId, $userId, $publica) {
        try {
            // Busca a técnica
            $tecnica = Tecnicas::where('id', $tecnicaId)
                           ->where('usuario_id', $userId)
                           ->first();
            
            if (!$tecnica) {
                return [
                    'success' => false,
                    'message' => 'Técnica não encontrada ou não pertence ao usuário',
                    'data' => null
                ];
            }
            
            // Atualiza a visibilidade
            $tecnica->publica = $publica;
            $tecnica->save();
            
            return [
                'success' => true,
                'message' => 'Visibilidade atualizada com sucesso',
                'data' => [
                    'id' => $tecnica->id,
                    'publica' => (bool)$tecnica->publica
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao alterar visibilidade: ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao alterar visibilidade: ' . $th->getMessage(),
                'data' => null
            ];
        }
    }
    
    /**
     * Verifica se uma posição existe e, se não, cria na tabela posicoes
     * 
     * @param string $nomePosicao Nome da posição
     * @param int $userId ID do usuário
     * @return bool Resultado da operação
     */
    private static function verificarECriarPosicao($nomePosicao, $userId) {
        try {
            // Verifica se a posição já existe (padrão ou do usuário)
            $posicao = Posicoes::where('nome', $nomePosicao)
                               ->where(function($query) use ($userId) {
                                   $query->where('padrao', true)
                                         ->orWhere('usuario_id', $userId);
                               })
                               ->first();
            
            // Se não existir, cria a posição para o usuário
            if (!$posicao) {
                Posicoes::create([
                    'nome' => $nomePosicao,
                    'usuario_id' => $userId,
                    'padrao' => false
                ]);
            }
            
            return true;
        } catch (\Throwable $th) {
            Logger::log('Erro ao verificar/criar posição: ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Salva os arquivos de vídeo e poster
     * 
     * @param array $videoData Dados do arquivo de vídeo
     * @return array|false Array com nomes dos arquivos ou false em caso de erro
     */
    private static function salvarArquivosVideo($videoData) {
        try {
            // Verifica se diretórios existem, se não, cria
            $diretorioVideos = __DIR__ . '/../admin/assets/imagens/arquivos/tecnicas/videos/';
            $diretorioPosters = __DIR__ . '/../admin/assets/imagens/arquivos/tecnicas/posters/';
            
            if (!file_exists($diretorioVideos)) {
                mkdir($diretorioVideos, 0755, true);
            }
            
            if (!file_exists($diretorioPosters)) {
                mkdir($diretorioPosters, 0755, true);
            }
            
            // Gera nomes únicos para os arquivos
            $timestamp = time();
            $videoNome = 'tecnica_' . $timestamp . '.mp4';
            $posterNome = 'tecnica_' . $timestamp . '.jpg';
            
            // Move o arquivo de vídeo
            move_uploaded_file($videoData['videoFile']['tmp_name'], $diretorioVideos . $videoNome);
            
            // Gerar poster simples apenas com fundo
            $videoPath = $diretorioVideos . $videoNome;
            $posterPath = $diretorioPosters . $posterNome;
            $width = 640;
            $height = 360;
            // Se vier do frontend, usa as dimensões informadas
            if (isset($videoData['video_width']) && $videoData['video_width'] > 0) {
                $width = (int)$videoData['video_width'];
            }
            if (isset($videoData['video_height']) && $videoData['video_height'] > 0) {
                $height = (int)$videoData['video_height'];
            }
            
            // Criar imagem de fundo
            $img = imagecreatetruecolor($width, $height);
            
            // Definir cor de fundo escura da identidade visual
            $corFundo = imagecolorallocate($img, 15, 20, 25); // var(--background) 215 28% 8%
            
            // Preencher fundo
            imagefill($img, 0, 0, $corFundo);
            
            // Salvar imagem
            imagejpeg($img, $posterPath, 90);
            imagedestroy($img);
            
            return [
                'video' => $videoNome,
                'poster' => $posterNome
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao salvar arquivos de vídeo: ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Remove os arquivos de vídeo e poster
     * 
     * @param string $videoNome Nome do arquivo de vídeo
     * @param string $posterNome Nome do arquivo de poster
     * @return bool Sucesso da operação
     */
    private static function removerArquivosVideo($videoNome, $posterNome) {
        try {
            if (!empty($videoNome)) {
                $videoPath = __DIR__ . '/../admin/assets/imagens/arquivos/tecnicas/videos/' . $videoNome;
                if (file_exists($videoPath)) {
                    unlink($videoPath);
                }
            }
            
            if (!empty($posterNome)) {
                $posterPath = __DIR__ . '/../admin/assets/imagens/arquivos/tecnicas/posters/' . $posterNome;
                if (file_exists($posterPath)) {
                    unlink($posterPath);
                }
            }
            
            return true;
        } catch (\Throwable $th) {
            Logger::log('Erro ao remover arquivos de vídeo: ' . $th->getMessage(), 'ERROR');
            return false;
        }
    }


    // criando posição
    public static function adicionarPosicao($userId, $nome) {
        try {
            $res = Posicoes::create([
                'usuario_id' => $userId,
                'nome' => $nome
            ]);

            return [
                'success' => true,
                'message' => 'Posição adicionada com sucesso',
                'data' => [
                    'nome' => $res->nome,
                    'criada_em' => $res->created_at->format('Y-m-d')
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao adicionar posição para usuário ID ' . $userId . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao adicionar posição',
                'data' => null
            ];
        }
    }

    // deletar posição
    public static function deletarPosicao($userId, $nome) {
        try {
            // Verifica se a posição existe e pertence ao usuário
            $posicao = Posicoes::where('nome', $nome)
                               ->where('usuario_id', $userId)
                               ->first();
            
            if (!$posicao) {
                return [
                    'success' => false,
                    'message' => 'Posição não encontrada ou não pertence ao usuário',
                    'data' => null
                ];
            }

            // Deleta a posição
            $posicao->delete();

            return [
                'success' => true,
                'message' => 'Posição deletada com sucesso',
                'data' => null
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao deletar posição para usuário ID ' . $userId . ': ' . $th->getMessage(), 'ERROR');
            return [
                'success' => false,
                'message' => 'Erro ao deletar posição',
                'data' => null
            ];
        }
    }
}