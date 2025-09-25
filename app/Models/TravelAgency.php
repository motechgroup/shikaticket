<?php
namespace App\Models;

class TravelAgency
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM travel_agencies WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM travel_agencies WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): bool
    {
        $stmt = db()->prepare('
            INSERT INTO travel_agencies 
            (company_name, contact_person, email, phone, password_hash, address, city, country, website, description) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        return $stmt->execute([
            $data['company_name'],
            $data['contact_person'],
            $data['email'],
            $data['phone'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['address'] ?? '',
            $data['city'] ?? '',
            $data['country'] ?? '',
            $data['website'] ?? '',
            $data['description'] ?? ''
        ]);
    }

    public static function updateCommission(int $agencyId, float $commissionRate): bool
    {
        $stmt = db()->prepare('UPDATE travel_agencies SET commission_rate = ? WHERE id = ?');
        return $stmt->execute([$commissionRate, $agencyId]);
    }

    public static function approve(int $agencyId): bool
    {
        $stmt = db()->prepare('UPDATE travel_agencies SET is_approved = 1 WHERE id = ?');
        return $stmt->execute([$agencyId]);
    }

    public static function getAll(): array
    {
        $stmt = db()->query('SELECT * FROM travel_agencies WHERE is_active = 1 ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function getPendingApprovals(): array
    {
        $stmt = db()->query('SELECT * FROM travel_agencies WHERE is_approved = 0 AND is_active = 1 ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function getFeaturedDestinations(int $limit = 6): array
    {
        $sql = '
            SELECT td.*, ta.company_name, ta.logo_path 
            FROM travel_destinations td 
            JOIN travel_agencies ta ON ta.id = td.agency_id 
            WHERE td.is_featured = 1 AND td.is_published = 1 
            AND td.departure_date > CURDATE() 
            AND ta.is_approved = 1 AND ta.is_active = 1
            ORDER BY td.created_at DESC 
            LIMIT ' . (int)$limit . '
        ';
        $stmt = db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function getAllPublishedDestinations(int $limit = 20, int $offset = 0): array
    {
        $sql = '
            SELECT td.*, ta.company_name, ta.logo_path 
            FROM travel_destinations td 
            JOIN travel_agencies ta ON ta.id = td.agency_id 
            WHERE td.is_published = 1 
            AND td.departure_date > CURDATE() 
            AND ta.is_approved = 1 AND ta.is_active = 1
            ORDER BY td.created_at DESC 
            LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset . '
        ';
        $stmt = db()->query($sql);
        return $stmt->fetchAll();
    }

    public static function getDestinationsByAgency(int $agencyId): array
    {
        $stmt = db()->prepare('
            SELECT * FROM travel_destinations 
            WHERE agency_id = ? 
            ORDER BY created_at DESC
        ');
        $stmt->execute([$agencyId]);
        return $stmt->fetchAll();
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
