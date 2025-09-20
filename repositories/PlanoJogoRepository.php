<?php

namespace Repositories;

use Models\PlanoJogo;
use Models\PlanoJogoNode;
use Core\Logger;

class PlanoJogoRepository {

    // Lista planos do usuário autenticado
    public static function listar($userId) {
        try {
            $planos = PlanoJogo::where('user_id', $userId)
                ->orderBy('criado_em', 'desc')
                ->get();

            $planosRet = [];
            foreach ($planos as $p) {
                $planosRet[] = [
                    'id' => (int)$p->id,
                    'nome' => $p->nome,
                    'descricao' => $p->descricao,
                    'categoria' => $p->categoria,
                    'dataCriacao' => self::toIso($p->criado_em),
                    'dataAtualizacao' => self::toIso($p->atualizado_em),
                ];
            }

            return [
                'data' => [ 'planos' => $planosRet ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao listar planos: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao listar planos' ];
        }
    }

    // Obter um plano por ID com árvore
    public static function obter($userId, $id) {
        try {
            $plano = PlanoJogo::where('id', $id)->where('user_id', $userId)->first();
            if (!$plano) {
                http_response_code(404);
                return [ 'message' => 'Plano não encontrado' ];
            }

            $nodes = PlanoJogoNode::where('plano_id', $plano->id)
                ->orderBy('parent_id')
                ->orderBy('ordem')
                ->get();

            $tree = self::montarArvore($nodes);

            return [
                'data' => [
                    'plano' => [
                        'id' => (int)$plano->id,
                        'nome' => $plano->nome,
                        'descricao' => $plano->descricao,
                        'categoria' => $plano->categoria,
                        'dataCriacao' => self::toIso($plano->criado_em),
                        'dataAtualizacao' => self::toIso($plano->atualizado_em),
                        'nodes' => $tree
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao obter plano: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao obter plano' ];
        }
    }

    // Criar plano
    public static function criar($userId, $dados) {
        try {
            if (empty($dados['nome'])) {
                http_response_code(400);
                return [ 'message' => 'Nome é obrigatório' ];
            }
            $plano = PlanoJogo::create([
                'user_id' => $userId,
                'nome' => $dados['nome'],
                'descricao' => $dados['descricao'] ?? null,
                'categoria' => $dados['categoria'] ?? null,
            ]);

            return [
                'data' => [
                    'plano' => [
                        'id' => (int)$plano->id,
                        'nome' => $plano->nome,
                        'descricao' => $plano->descricao,
                        'categoria' => $plano->categoria,
                        'dataCriacao' => self::toIso($plano->criado_em),
                        'dataAtualizacao' => self::toIso($plano->atualizado_em),
                        'nodes' => []
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao criar plano: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao criar plano' ];
        }
    }

    // Atualizar plano
    public static function atualizar($userId, $dados) {
        try {
            if (empty($dados['id'])) {
                http_response_code(400);
                return [ 'message' => 'ID é obrigatório' ];
            }
            $plano = PlanoJogo::where('id', (int)$dados['id'])->where('user_id', $userId)->first();
            if (!$plano) {
                http_response_code(404);
                return [ 'message' => 'Plano não encontrado' ];
            }
            $plano->update([
                'nome' => $dados['nome'] ?? $plano->nome,
                'descricao' => $dados['descricao'] ?? $plano->descricao,
                'categoria' => $dados['categoria'] ?? $plano->categoria,
            ]);
            $plano->refresh();

            return [
                'data' => [
                    'plano' => [
                        'id' => (int)$plano->id,
                        'nome' => $plano->nome,
                        'descricao' => $plano->descricao,
                        'categoria' => $plano->categoria,
                        'dataAtualizacao' => self::toIso($plano->atualizado_em)
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao atualizar plano: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao atualizar plano' ];
        }
    }

    // Excluir plano
    public static function excluir($userId, $id) {
        try {
            $plano = PlanoJogo::where('id', (int)$id)->where('user_id', $userId)->first();
            if (!$plano) {
                http_response_code(404);
                return [ 'message' => 'Plano não encontrado' ];
            }
            $plano->delete();
            return [ 'data' => [ 'success' => true ] ];
        } catch (\Throwable $th) {
            Logger::log('Erro ao excluir plano: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao excluir plano' ];
        }
    }

    // Adicionar nó
    public static function adicionarNode($userId, $dados) {
        try {
            $plano = PlanoJogo::where('id', (int)$dados['planoId'])->where('user_id', $userId)->first();
            if (!$plano) {
                http_response_code(404);
                return [ 'message' => 'Plano não encontrado' ];
            }

            $nodeData = $dados['node'] ?? [];
            if (empty($nodeData['nome']) || empty($nodeData['tipo'])) {
                http_response_code(400);
                return [ 'message' => 'Campos nome e tipo são obrigatórios' ];
            }

            $id = self::generateNodeId();
            $ordem = self::proximaOrdem($plano->id, $dados['parentId'] ?? null);

            // normaliza nomes dos arquivos para salvar apenas o nome (sem URL completa)
            $videoNome = isset($nodeData['video_url']) ? self::normalizeFilename($nodeData['video_url']) : null;
            $posterNome = isset($nodeData['video_poster']) ? self::normalizeFilename($nodeData['video_poster']) : null;

            PlanoJogoNode::create([
                'id' => $id,
                'plano_id' => $plano->id,
                'parent_id' => $dados['parentId'] ?? null,
                'nome' => $nodeData['nome'],
                'tipo' => $nodeData['tipo'],
                'descricao' => $nodeData['descricao'] ?? null,
                'tecnica_id' => $nodeData['tecnicaId'] ?? null,
                'categoria' => $nodeData['categoria'] ?? null,
                'posicao' => $nodeData['posicao'] ?? null,
                'passos' => $nodeData['passos'] ?? null,
                'observacoes' => $nodeData['observacoes'] ?? null,
                'video_url' => $videoNome,
                'video_poster' => $posterNome,
                'video' => $nodeData['video'] ?? null,
                'ordem' => $ordem,
            ]);

            // Retorna plano completo atualizado
            return self::obter($userId, $plano->id);
        } catch (\Throwable $th) {
            Logger::log('Erro ao adicionar nó: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao adicionar nó' ];
        }
    }

    // Remover nó
    public static function removerNode($userId, $planoId, $nodeId) {
        try {
            $plano = PlanoJogo::where('id', (int)$planoId)->where('user_id', $userId)->first();
            if (!$plano) {
                http_response_code(404);
                return [ 'message' => 'Plano não encontrado' ];
            }
            $node = PlanoJogoNode::where('id', $nodeId)->where('plano_id', $plano->id)->first();
            if (!$node) {
                http_response_code(404);
                return [ 'message' => 'Nó não encontrado' ];
            }
            $node->delete(); // ON DELETE CASCADE cuida dos filhos

            // Retorna plano completo atualizado
            return self::obter($userId, $plano->id);
        } catch (\Throwable $th) {
            Logger::log('Erro ao remover nó: ' . $th->getMessage(), 'ERROR');
            http_response_code(500);
            return [ 'message' => 'Erro ao remover nó' ];
        }
    }

    // ===== Helpers =====
    private static function montarArvore($nodes) {
        $baseUrl = $_ENV['BASE_URL'] ?? '';
        $map = [];
        foreach ($nodes as $n) {
            // Garantir que vamos responder sempre com URL pública construída a partir do nome salvo
            $videoFile = self::normalizeFilename($n->video_url);
            $posterFile = self::normalizeFilename($n->video_poster);
            $videoUrl = $videoFile ? ($baseUrl . 'assets/imagens/arquivos/tecnicas/videos/' . $videoFile) : null;
            $videoPoster = $posterFile ? ($baseUrl . 'assets/imagens/arquivos/tecnicas/posters/' . $posterFile) : null;
            $map[$n->id] = [
                'id' => $n->id,
                'parentId' => $n->parent_id,
                'nome' => $n->nome,
                'tipo' => $n->tipo,
                'descricao' => $n->descricao,
                'tecnicaId' => $n->tecnica_id ? (int)$n->tecnica_id : null,
                'categoria' => $n->categoria,
                'posicao' => $n->posicao,
                'passos' => $n->passos ?? [],
                'observacoes' => $n->observacoes ?? [],
                'video_url' => $videoUrl,
                'video_poster' => $videoPoster,
                'video' => $n->video,
                'children' => [],
            ];
        }
        $roots = [];
        foreach ($map as $id => &$node) {
            if ($node['parentId'] && isset($map[$node['parentId']])) {
                $map[$node['parentId']]['children'][] = &$node;
            } else {
                $roots[] = &$node;
            }
        }
        // Ordenar children por 'ordem' já vem do query; manter ordem de inserção
        return $roots;
    }

    private static function toIso($dt) {
        if (!$dt) return null;
        // $dt pode vir como string Y-m-d H:i:s; converter para ISO UTC com Z
        $d = new \DateTime(is_string($dt) ? $dt : (string)$dt);
        $d->setTimezone(new \DateTimeZone('UTC'));
        return $d->format('Y-m-d\TH:i:s\Z');
    }

    private static function generateNodeId() {
        // Gera ID curto base36 com tempo + random
        $time = base_convert((string)time(), 10, 36);
        $rand = base_convert(bin2hex(random_bytes(4)), 16, 36);
        return 'n' . $time . $rand;
    }

    private static function proximaOrdem($planoId, $parentId) {
        $max = PlanoJogoNode::where('plano_id', $planoId)
            ->where(function($q) use ($parentId) {
                if ($parentId === null || $parentId === '') {
                    $q->whereNull('parent_id');
                } else {
                    $q->where('parent_id', $parentId);
                }
            })
            ->max('ordem');
        return $max !== null ? ((int)$max + 1) : 0;
    }

    private static function normalizeFilename($value) {
        if (!$value) return null;
        // remove espaços e converte para string
        $str = trim((string)$value);
        // se vier uma URL completa ou caminho, extrai apenas o nome do arquivo
        $basename = basename(parse_url($str, PHP_URL_PATH) ?: $str);
        return $basename ?: null;
    }
}
