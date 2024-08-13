<?php

namespace App\Controllers;
use Core\View;
use Forms\Login;
use Forms\AddUser;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/Paris');

class Security
{
    public function homePage(): void
    {
        $myView = new View("Security/home", "neutral");
    }

    public function login(): void
    {

        $formLogin = new Login();
        $configLogin = $formLogin->getConfig();
        $errorsLogin = [];
        $successLogin = [];

        $myView = new View("Security/login", "neutral");

        $myView->assign("configForm", $configLogin);
        $myView->assign("errorsForm", $errorsLogin);
        $myView->assign("successForm", $successLogin);
    }

    public function register(): void
    {
        $form = new AddUser();
        $config = $form->getConfig();

        $errors = [];
        $success = [];

        $myView = new View("Security/register", "neutral");
        $myView->assign("configForm", $config);
        $myView->assign("errorsForm", $errors);
        $myView->assign("successForm", $success);

    }
    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
            // Start the session
            session_start();

            // Unset all session variables
            $_SESSION = array();

            // Destroy the session
            session_destroy();

            // Redirect the user to the login page or any other appropriate page
            header("Location: /login");
            exit();
        }
    }
}