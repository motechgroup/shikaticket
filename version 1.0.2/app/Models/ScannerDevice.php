<?php
namespace App\Models;

class ScannerDevice
{
	public static function findByOrganizer(int $organizerId): array
	{
		$stmt = db()->prepare('SELECT * FROM scanner_devices WHERE organizer_id = ? ORDER BY created_at DESC');
		$stmt->execute([$organizerId]);
		return $stmt->fetchAll();
	}

	public static function findByDeviceCode(string $deviceCode): ?array
	{
		$stmt = db()->prepare('SELECT * FROM scanner_devices WHERE device_code = ? AND is_active = 1 LIMIT 1');
		$stmt->execute([$deviceCode]);
		$row = $stmt->fetch();
		return $row ?: null;
	}

	public static function create(int $organizerId, string $deviceName): int
	{
		$deviceCode = strtoupper(substr(md5(uniqid()), 0, 8));
		$stmt = db()->prepare('INSERT INTO scanner_devices (organizer_id, device_name, device_code) VALUES (?, ?, ?)');
		$stmt->execute([$organizerId, $deviceName, $deviceCode]);
		return (int)db()->lastInsertId();
	}

	public static function update(int $id, string $deviceName, bool $isActive): void
	{
		$stmt = db()->prepare('UPDATE scanner_devices SET device_name = ?, is_active = ? WHERE id = ?');
		$stmt->execute([$deviceName, $isActive ? 1 : 0, $id]);
	}

	public static function delete(int $id): void
	{
		$stmt = db()->prepare('DELETE FROM scanner_devices WHERE id = ?');
		$stmt->execute([$id]);
	}

	public static function getAssignedEvents(int $scannerDeviceId): array
	{
		$stmt = db()->prepare('
			SELECT e.*, esa.assigned_at, esa.is_active as assignment_active
			FROM event_scanner_assignments esa
			JOIN events e ON e.id = esa.event_id
			WHERE esa.scanner_device_id = ? AND esa.is_active = 1
			ORDER BY esa.assigned_at DESC
		');
		$stmt->execute([$scannerDeviceId]);
		return $stmt->fetchAll();
	}

	public static function isAssignedToEvent(int $scannerDeviceId, int $eventId): bool
	{
		$stmt = db()->prepare('SELECT 1 FROM event_scanner_assignments WHERE scanner_device_id = ? AND event_id = ? AND is_active = 1 LIMIT 1');
		$stmt->execute([$scannerDeviceId, $eventId]);
		return $stmt->fetch() !== false;
	}

}
