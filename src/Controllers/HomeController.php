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
        // 1. On intercepte les mots-clés tapés dans la barre de recherche
        $keyword = $_GET['q'] ?? '';
        $location = $_GET['lieu'] ?? '';

        // 2. S'il y a une recherche, on filtre, sinon on prend tout !
        if (!empty($keyword) || !empty($location)) {
            $offres = \App\Models\Offre::search($keyword, $location);
        } else {
            $offres = \App\Models\Offre::getAll(); 
        }

        // 3. Gestion des favoris pour l'affichage des drapeaux bleus
        $mesFavorisIds = [];
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 4. On récupère les statistiques en temps réel
        $stats = \App\Models\Stats::getDashboardStats();

        // 5. On envoie tout à la vue (en gardant bien 'accueil.html.twig')
        echo $this->twig->render('accueil.html.twig', [
            'offres' => $offres,
            'mesFavorisIds' => $mesFavorisIds,
            'stats' => $stats,
            'search_q' => $keyword,      // On renvoie le mot-clé pour ne pas vider la barre
            'search_lieu' => $location   // On renvoie le lieu
        ]);
    }
}