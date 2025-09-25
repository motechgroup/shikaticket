<?php
namespace App\Controllers;

use App\Models\TravelAgency;

class TravelAuthController
{
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($email === '' || $password === '') {
                flash_set('error', 'Email and password are required.');
                redirect(base_url('/travel/login'));
            }

            $agency = TravelAgency::findByEmail($email);
            if (!$agency) {
                flash_set('error', 'Invalid credentials.');
                redirect(base_url('/travel/login'));
            }

            // Agencies can log in even if phone not verified; approval required
            if (!$agency['is_approved']) {
                flash_set('error', 'Your account is pending approval. Please contact support.');
                redirect(base_url('/travel/login'));
            }

            if (!TravelAgency::verifyPassword($password, $agency['password_hash'])) {
                flash_set('error', 'Invalid credentials.');
                redirect(base_url('/travel/login'));
            }

            // Set session
            $_SESSION['travel_agency_id'] = $agency['id'];
            $_SESSION['travel_agency_name'] = $agency['company_name'];
            $_SESSION['travel_agency_email'] = $agency['email'];

            flash_set('success', 'Welcome back, ' . $agency['company_name']);
            redirect(base_url('/travel/dashboard'));
        }

        view('travel/auth/login');
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'company_name' => trim($_POST['company_name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'address' => trim($_POST['address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'country' => trim($_POST['country'] ?? ''),
                'website' => trim($_POST['website'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            // Validation
            if ($data['company_name'] === '' || $data['contact_person'] === '' || 
                $data['email'] === '' || $data['phone'] === '' || $data['password'] === '') {
                flash_set('error', 'All required fields must be filled.');
                redirect(base_url('/travel/register'));
            }

            if (strlen($data['password']) < 6) {
                flash_set('error', 'Password must be at least 6 characters.');
                redirect(base_url('/travel/register'));
            }

            // Check if email already exists
            if (TravelAgency::findByEmail($data['email'])) {
                flash_set('error', 'Email already registered.');
                redirect(base_url('/travel/register'));
            }

            // Normalize website to https scheme if missing
            if ($data['website'] !== '' && !preg_match('~^https?://~i', $data['website'])) {
                $data['website'] = 'https://' . $data['website'];
            }
            // Normalize phone by country code if provided
            $dialByCountry = [
                'Kenya' => '+254', 'Tanzania' => '+255', 'Uganda' => '+256', 'Rwanda' => '+250',
                'South Africa' => '+27', 'Zambia' => '+260', 'Malawi' => '+265'
            ];
            $digits = preg_replace('/\D+/', '', $data['phone']);
            if ($digits !== '') {
                $digits = ltrim($digits, '0');
                $prefix = $dialByCountry[$data['country']] ?? '';
                if ($prefix !== '') { $data['phone'] = $prefix . ' ' . $digits; }
                else if ($data['phone'][0] !== '+') { $data['phone'] = '+' . $digits; }
            }

            try {
                if (TravelAgency::create($data)) {
                    flash_set('success', 'Registration successful! Your account is pending approval.');
                    redirect(base_url('/travel/login'));
                } else {
                    flash_set('error', 'Registration failed. Please try again.');
                    redirect(base_url('/travel/register'));
                }
            } catch (\PDOException $e) {
                flash_set('error', 'Registration failed. Please try again.');
                redirect(base_url('/travel/register'));
            }
        }

        view('travel/auth/register');
    }

    public function logout(): void
    {
        unset($_SESSION['travel_agency_id']);
        unset($_SESSION['travel_agency_name']);
        unset($_SESSION['travel_agency_email']);
        
        flash_set('success', 'Logged out successfully.');
        redirect(base_url('/travel/login'));
    }
}
