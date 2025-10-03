<?php
namespace App\Services;

use App\Models\Setting;

class SmsTemplates
{
    /**
     * Render an SMS template by key with placeholders replaced from $data
     */
    public static function render(string $key, array $data = []): string
    {
        $raw = Setting::get('sms.' . $key, '');
        if ($raw === '') { return ''; }
        $out = $raw;
        foreach ($data as $k => $v) {
            $out = str_replace('{{' . $k . '}}', (string)$v, $out);
        }
        return $out;
    }
}

?>


