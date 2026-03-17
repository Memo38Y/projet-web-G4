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
        $vraiesOffres = \App\Models\Offre::getLastThree();

        $mesFavorisIds = [];

        // Si c'est un étudiant connecté, on utilise ta fonction existante !
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            
            // 1. On récupère toutes les données des offres favorites
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            
            // 2. On extrait UNIQUEMENT la colonne 'Id_OFFRE' pour en faire une liste simple
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        echo $this->twig->render('accueil.html.twig', [
            'dernieresOffres' => $vraiesOffres,
            'mesFavorisIds' => $mesFavorisIds
        ]);
    }
}