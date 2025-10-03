<?php
namespace App\Services;

use App\Models\Setting;

class Sms
{
    private string $sid;
    private string $token;
    private string $from;
    private string $provider;
    // TextSMS
    private string $txApiKey;
    private string $txPartnerId;
    private string $txShortcode;
    private string $txDomain;

    public function __construct()
    {
        $this->sid = Setting::get('twilio.sid', '');
        $this->token = Setting::get('twilio.token', '');
        $this->from = Setting::get('twilio.from', '');
        $this->provider = Setting::get('sms.provider', 'twilio');
        $this->txApiKey = Setting::get('textsms.api_key', '');
        $this->txPartnerId = Setting::get('textsms.partner_id', '');
        $this->txShortcode = Setting::get('textsms.shortcode', '');
        $this->txDomain = Setting::get('textsms.domain', 'sms.textsms.co.ke');

        // Auto-select textsms if credentials exist to ensure the integrated gateway is used
        if ($this->txApiKey !== '' && $this->txPartnerId !== '' && $this->txShortcode !== '') {
            $this->provider = 'textsms';
        }
    }

    public function isConfigured(): bool
    {
        if ($this->provider === 'twilio') {
            return $this->sid !== '' && $this->token !== '' && $this->from !== '';
        }
        if ($this->provider === 'textsms') {
            return $this->txApiKey !== '' && $this->txPartnerId !== '' && $this->txShortcode !== '';
        }
        return false;
    }

    public function send(string $to, string $message): bool
    {
        if (!$this->isConfigured()) { return false; }
        $to = $this->normalizeMsisdn($to);
        $ok = false; $resp = null;
        if ($this->provider === 'twilio') {
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($this->sid) . '/Messages.json';
            $payload = http_build_query([
                'From' => $this->from,
                'To' => $to,
                'Body' => $message,
            ]);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $this->sid . ':' . $this->token,
            ]);
            $resp = curl_exec($ch);
            if ($resp !== false) {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $ok = $code >= 200 && $code < 300;
            }
        }
        if ($this->provider === 'textsms') {
            $endpoint = 'https://' . $this->txDomain . '/api/services/sendsms/';
            $payload = json_encode([
                'apikey' => $this->txApiKey,
                'partnerID' => $this->txPartnerId,
                'message' => $message,
                'shortcode' => $this->txShortcode,
                'mobile' => $to,
            ]);
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ]);
            $resp = curl_exec($ch);
            if ($resp !== false) {
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($code >= 200 && $code < 300) {
                    $json = json_decode($resp, true);
                    // TextSMS sometimes returns a misspelled key 'respose-code'; handle both
                    $respCode = null;
                    if (isset($json['responses'][0]['response-code'])) { $respCode = (int)$json['responses'][0]['response-code']; }
                    if ($respCode === null && isset($json['responses'][0]['respose-code'])) { $respCode = (int)$json['responses'][0]['respose-code']; }
                    if ($respCode !== null) {
                        $ok = ($respCode === 200);
                    } else {
                        // Fallback: treat HTTP 2xx as success if payload missing code
                        $ok = true;
                    }
                }
            }
        }
        // Log with detailed information
        try {
            $stmt = \db()->prepare('INSERT INTO sms_logs (provider, recipient, message, status, response) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$this->provider, $to, $message, $ok ? 'sent' : 'failed', $resp]);
            
            // Log to error log for debugging
            error_log("SMS Send Attempt - Provider: {$this->provider}, To: {$to}, Status: " . ($ok ? 'SUCCESS' : 'FAILED') . ", Response: " . substr($resp, 0, 200));
        } catch (\Throwable $e) {
            error_log("SMS Logging Error: " . $e->getMessage());
        }
        return $ok;
    }

    private function normalizeMsisdn(string $raw): string
    {
        $n = preg_replace('/\D+/', '', $raw);
        if ($n === '') { return $raw; }
        if (strpos($n, '254') === 0) { return $n; }
        if ($n[0] === '0') { return '254' . substr($n, 1); }
        // Handle 7XXXXXXXX style
        if (strlen($n) === 9 && ($n[0] === '7' || $n[0] === '1')) { return '254' . $n; }
        return $n;
    }
}

?>


