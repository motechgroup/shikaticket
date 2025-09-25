<?php
namespace App\Controllers;
use App\Models\Event;

class HomeController
{
	public function index(): void
	{
        $featuredEvents = Event::featured();
        $events = Event::available();
        $banners = db()->query('SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC')->fetchAll();
        $featuredDestinations = \App\Models\TravelAgency::getFeaturedDestinations(6);
        view('home', compact('featuredEvents', 'events', 'banners', 'featuredDestinations'));
	}
}


