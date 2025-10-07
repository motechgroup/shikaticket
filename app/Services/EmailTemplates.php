<?php
namespace App\Services;

use App\Models\Setting;

class EmailTemplates
{
	public static function render(string $key, array $vars = []): string
	{
		$html = Setting::get('email.' . $key, '');
		if ($html === '' && in_array($key, ['user_welcome','organizer_approved','travel_agency_welcome','travel_agency_approved'], true)) {
			// Load defaults if not set for common templates
			$path = __DIR__ . '/../Views/emails/' . $key . '.php';
			if (file_exists($path)) {
				ob_start(); extract($vars, EXTR_SKIP); include $path; $content = ob_get_clean();
				ob_start(); include __DIR__ . '/../Views/emails/layout.php'; $html = ob_get_clean();
			}
		}
		if ($html === '') { return ''; }
		foreach ($vars as $k => $v) {
			$html = str_replace('{{' . $k . '}}', htmlspecialchars((string)$v), $html);
		}
		return $html;
	}
}


