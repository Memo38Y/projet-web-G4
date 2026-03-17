<?php

namespace App\Controllers;

class ProfilController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        // LE VIGILE : Si le visiteur n'a pas de session, on le vire vers la connexion
        if (!isset($_SESSION['user'])) {
            header('Location: /connexion');
            exit;
        }

        // S'il est connecté, on affiche sa belle page
        echo $this->twig->render('profil.html.twig');
    }
}