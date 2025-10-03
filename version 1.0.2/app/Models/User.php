<?php
namespace App\Models;

class User
{
	public static function findById(int $id): ?array
	{
		$stmt = db()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
		$stmt->execute([$id]);
		$row = $stmt->fetch();
		return $row ?: null;
	}
	public static function findByPhone(string $phone): ?array
	{
		$stmt = db()->prepare('SELECT * FROM users WHERE phone = ? LIMIT 1');
		$stmt->execute([$phone]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function create(string $phone, string $email, string $password): int
	{
		$passwordHash = password_hash($password, PASSWORD_DEFAULT);
		$stmt = db()->prepare('INSERT INTO users (phone, email, password_hash) VALUES (?, ?, ?)');
		$stmt->execute([$phone, $email, $passwordHash]);
		return (int)db()->lastInsertId();
	}
}


