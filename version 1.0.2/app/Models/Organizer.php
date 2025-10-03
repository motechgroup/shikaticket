<?php
namespace App\Models;

class Organizer
{
	public static function findByEmail(string $email): ?array
	{
		$stmt = db()->prepare('SELECT * FROM organizers WHERE email = ? LIMIT 1');
		$stmt->execute([$email]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function create(string $fullName, string $phone, string $email, string $password): int
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$stmt = db()->prepare('INSERT INTO organizers (full_name, phone, email, password_hash) VALUES (?, ?, ?, ?)');
		$stmt->execute([$fullName, $phone, $email, $passwordHash]);
		return (int)db()->lastInsertId();
	}

	public static function approve(int $organizerId): void
	{
		$stmt = db()->prepare('UPDATE organizers SET is_approved = 1 WHERE id = ?');
		$stmt->execute([$organizerId]);
	}
}


