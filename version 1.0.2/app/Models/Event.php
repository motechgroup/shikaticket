<?php
namespace App\Models;

class Event
{
	public static function featured(int $limit = 6): array
	{
		// Only show featured events that haven't expired yet
		$stmt = db()->prepare('SELECT * FROM events WHERE is_featured = 1 AND is_published = 1 AND (event_date > CURDATE() OR (event_date = CURDATE() AND event_time > CURTIME())) ORDER BY created_at DESC LIMIT ?');
		$stmt->bindValue(1, $limit, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function available(int $limit = 12): array
	{
		// Only show events that haven't expired yet
		$stmt = db()->prepare('SELECT * FROM events WHERE is_published = 1 AND (event_date > CURDATE() OR (event_date = CURDATE() AND event_time > CURTIME())) ORDER BY created_at DESC LIMIT ?');
		$stmt->bindValue(1, $limit, \PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	public static function create(array $data): int
	{
		$stmt = db()->prepare('INSERT INTO events (organizer_id, title, description, category, event_date, event_time, venue, poster_path, capacity, price, regular_price, early_bird_price, early_bird_until, vip_price, vvip_price, group_price, group_size, currency, dress_code, lineup, is_featured, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->execute([
			$data['organizer_id'],
			$data['title'],
			$data['description'] ?? null,
			$data['category'] ?? null,
			$data['event_date'] ?? null,
			$data['event_time'] ?? null,
			$data['venue'] ?? null,
			$data['poster_path'] ?? null,
			$data['capacity'] ?? null,
			$data['price'] ?? 0,
			$data['regular_price'] ?? null,
			$data['early_bird_price'] ?? null,
			$data['early_bird_until'] ?? null,
			$data['vip_price'] ?? null,
			$data['vvip_price'] ?? null,
			$data['group_price'] ?? null,
			$data['group_size'] ?? null,
			$data['currency'] ?? 'KES',
			$data['dress_code'] ?? null,
			$data['lineup'] ?? null,
			$data['is_featured'] ?? 0,
			$data['is_published'] ?? 0,
		]);
		return (int)db()->lastInsertId();
	}
}


