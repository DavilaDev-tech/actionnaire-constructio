<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;

class SmsService
{
    protected $sms;

    public function __construct()
    {
        $at = new AfricasTalking(
            config('services.africastalking.username'),
            config('services.africastalking.api_key')
        );
        $this->sms = $at->sms();
    }

    // ── Envoyer un SMS ──
    public function envoyer(string $telephone, string $message): bool
{
    try {
        $telephone = $this->formaterNumero($telephone);

        $params = [
            'to'      => $telephone,
            'message' => $message,
        ];

        // Ajouter le from seulement s'il est défini
        $from = config('services.africastalking.from');
        if (!empty($from)) {
            $params['from'] = $from;
        }

        $result = $this->sms->send($params);

        Log::info("SMS envoyé à {$telephone} : " . json_encode($result));
        return true;

    } catch (\Exception $e) {
        Log::error("Erreur SMS à {$telephone} : " . $e->getMessage());
        return false;
    }
}
    // ── Formater numéro camerounais ──
    private function formaterNumero(string $telephone): string
    {
        // Supprimer les espaces et tirets
        $telephone = preg_replace('/[\s\-]/', '', $telephone);

        // Ajouter l'indicatif +237 si absent
        if (substr($telephone, 0, 4) === '+237') {
            return $telephone;
        }

        if (substr($telephone, 0, 3) === '237') {
            return '+' . $telephone;
        }

        return '+237' . $telephone;
    }
}