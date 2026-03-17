<?php

namespace App\Controllers;

class AuthController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function login()
    {
        // On affiche simplement la vue du formulaire
        echo $this->twig->render('connexion.html.twig');
    }
}