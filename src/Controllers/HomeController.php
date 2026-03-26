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
        // 1. On récupère les offres pour l'affichage classique
        $dernieresOffres = \App\Models\Offre::getAll(); 

        // 2. Gestion des favoris pour l'affichage des drapeaux bleus
        $mesFavorisIds = [];
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 3. NOUVEAU : On récupère les statistiques en temps réel !
        $stats = \App\Models\Stats::getDashboardStats();

        // 4. On envoie tout à la vue
        echo $this->twig->render('accueil.html.twig', [
            'dernieresOffres' => $dernieresOffres,
            'mesFavorisIds' => $mesFavorisIds,
            'stats' => $stats // On passe le tableau de stats à Twig
        ]);
    }
}