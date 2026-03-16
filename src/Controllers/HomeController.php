<?php

namespace App\Controllers;

// On importe notre nouveau Modèle !
use App\Models\Offre;

class HomeController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        // On va chercher les vraies données dans la BDD
        $vraiesOffres = Offre::getLastThree();

        // On envoie ces données à la vue Twig
        echo $this->twig->render('accueil.html.twig', [
            'dernieresOffres' => $vraiesOffres
        ]);
    }
}