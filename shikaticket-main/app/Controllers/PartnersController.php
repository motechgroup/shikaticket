<?php
namespace App\Controllers;

class PartnersController
{
    public function index(): void
    {
        view('partners/index');
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($name === '' || $email === '') { redirect(base_url('/partners')); }
        $phone = trim($_POST['phone'] ?? '');
        $org = trim($_POST['organization'] ?? '');
        $category = trim($_POST['category'] ?? ''); // categories: Corporate, Celebrity, NGO, Institution, Other
        $message = trim($_POST['message'] ?? '');
        $stmt = db()->prepare('INSERT INTO partners (name, email, phone, organization, category, message) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone, $org, $category, $message]);
        flash_set('success', 'Thanks for your interest. We will get back to you.');
        redirect(base_url('/partners'));
    }
}


