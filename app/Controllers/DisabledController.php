<?php
namespace App\Controllers;

class DisabledController
{
    public function notFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/plain');
        echo 'Not Found';
    }
}


