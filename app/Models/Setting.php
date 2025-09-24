<?php
namespace App\Models;

class Setting
{
	public static function get(string $key, $default = null)
	{
		$stmt = db()->prepare('SELECT `value` FROM settings WHERE `key` = ? LIMIT 1');
		$stmt->execute([$key]);
		$row = $stmt->fetch();
		if (!$row) { return $default; }
		return $row['value'];
	}

	public static function set(string $key, $value): void
	{
		$stmt = db()->prepare('INSERT INTO settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
		$stmt->execute([$key, $value]);
	}

	public static function all(): array
	{
		$result = db()->query('SELECT `key`, `value` FROM settings');
		$map = [];
		foreach ($result->fetchAll() as $row) {
			$map[$row['key']] = $row['value'];
		}
		return $map;
	}
}


