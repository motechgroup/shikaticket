<?php
namespace App\Controllers;

class AdminCategoryController
{
    public function index(): void
    {
        require_admin();
        $rows = db()->query('SELECT * FROM event_categories ORDER BY sort_order ASC, name ASC')->fetchAll();
        view('admin/categories/index', ['categories'=>$rows]);
    }

    public function form(): void
    {
        require_admin();
        $id = (int)($_GET['id'] ?? 0);
        $row = null;
        if ($id > 0) { $stmt = db()->prepare('SELECT * FROM event_categories WHERE id = ?'); $stmt->execute([$id]); $row = $stmt->fetch(); }
        view('admin/categories/form', ['category'=>$row]);
    }

    public function save(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $sort = (int)($_POST['sort_order'] ?? 0);
        $active = isset($_POST['is_active']) ? 1 : 0;
        if ($name === '' || $slug === '') { flash_set('error', 'Name and slug are required.'); redirect(base_url('/admin/categories')); }
        if ($id > 0) {
            $stmt = db()->prepare('UPDATE event_categories SET name = ?, slug = ?, is_active = ?, sort_order = ?, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$name, $slug, $active, $sort, $id]);
        } else {
            $stmt = db()->prepare('INSERT INTO event_categories (name, slug, is_active, sort_order, created_at) VALUES (?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $slug, $active, $sort]);
        }
        flash_set('success', 'Category saved.');
        redirect(base_url('/admin/categories'));
    }

    public function delete(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { db()->prepare('DELETE FROM event_categories WHERE id = ?')->execute([$id]); }
        flash_set('success', 'Category deleted.');
        redirect(base_url('/admin/categories'));
    }

    public function toggle(): void
    {
        require_admin(); verify_csrf();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) { db()->prepare('UPDATE event_categories SET is_active = 1 - is_active WHERE id = ?')->execute([$id]); }
        flash_set('success', 'Category updated.');
        redirect(base_url('/admin/categories'));
    }
}


