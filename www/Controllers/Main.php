<?php
namespace App\Controllers;
use Core\View;

require __DIR__ . '/../vendor/autoload.php';
class Main
{
    public function home(): void
    {
        $myView = new View("Main/home", "back");

    }
}
