<?php

namespace Repositories;

use Models\Plans;
use Models\Contatos;
use Models\Avaliacoes;
use Core\Logger;

class SistemaRepository {
   // buscar todos os planos
    public static function getAllPlans() {
        try {
            $plans = Plans::all();
            return $plans;
        } catch (\Exception $e) {
            Logger::log("Erro ao buscar todos os planos: " . $e->getMessage());
            return [];
        }
    }

    // atualizar um plano
    public static function updatePlan($id, $data) {
        try {
            $plan = Plans::find($id);
            if ($plan) {
                $plan->update($data);
                return $plan;
            } else {
                Logger::log("Plano com ID $id não encontrado para atualização.");
                return null;
            }
        } catch (\Exception $e) {
            Logger::log("Erro ao atualizar o plano com ID $id: " . $e->getMessage());
            return null;
        }
    }

    // exluir um plano
    public static function deletePlan($id) {
        try {
            $plan = Plans::find($id);
            if ($plan) {
                $plan->delete();
                return true;
            } else {
                Logger::log("Plano com ID $id não encontrado para exclusão.");
                return false;
            }
        } catch (\Exception $e) {
            Logger::log("Erro ao excluir o plano com ID $id: " . $e->getMessage());
            return false;
        }
    }

    // buscando contato com id = 1
    public static function getContatoById() {
        try {
            $contato = Contatos::where('id', 1)->first();
            return $contato;
        } catch (\Exception $e) {
            Logger::log("Erro ao buscar o contato com ID 1: " . $e->getMessage());
            return false;
        }
    }

    // salvando dados de contato
    public static function enviarEmailContato($data) {
        require_once __DIR__ . '/../helpers/envio-emails/enviar-dados-contato.php';
        $emailEnviado = sendContactEmail($data);

        if($emailEnviado) {
            return [
                'success' => true,
                'message' => 'E-mail enviado com sucesso!'
            ];
        } else {
            Logger::log('Falha ao enviar e-mail de contato do site', 'WARNING');
            return [
                'success' => false,
                'message' => 'Houve um problema ao enviar o e-mail de contato',
            ];
        }
    }

    // salvando dados de suporte
    public static function enviarEmailSuporte($data) {
        require_once __DIR__ . '/../helpers/envio-emails/enviar-dados-suporte.php';
        $emailEnviado = sendSupportEmail($data);

        if($emailEnviado) {
            return [
                'success' => true,
                'message' => 'E-mail enviado com sucesso!'
            ];
        } else {
            Logger::log('Falha ao enviar e-mail de suporte do site', 'WARNING');
            return [
                'success' => false,
                'message' => 'Houve um problema ao enviar o e-mail de suporte',
            ];
        }
    }

    // puxando todas as avaliações
    public static function getAllAvaliacoes() {
        try {
            $avaliacoes = Avaliacoes::all();
            return $avaliacoes;
        } catch (\Exception $e) {
            Logger::log("Erro ao buscar todas as avaliações: " . $e->getMessage());
            return [];
        }
    }
}
