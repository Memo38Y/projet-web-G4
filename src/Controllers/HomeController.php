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
        // 1. On récupère TOUTES les offres
        $offres = \App\Models\Offre::getAll(); 

        // 2. Gestion des favoris pour l'affichage des drapeaux bleus
        $mesFavorisIds = [];
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 3. On récupère les statistiques en temps réel
        $stats = \App\Models\Stats::getDashboardStats();

        // 4. On envoie tout à la vue (on utilise 'offres' au lieu de 'dernieresOffres')
        echo $this->twig->render('accueil.html.twig', [
            'offres' => $offres,
            'mesFavorisIds' => $mesFavorisIds,
            'stats' => $stats
        ]);
    }
}