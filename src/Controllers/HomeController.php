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
        // 1. On intercepte les mots-clés
        $keyword = $_GET['q'] ?? '';
        $location = $_GET['lieu'] ?? '';
        
        // 2. Gestion de la Pagination
        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1; // Page 1 par défaut
        $limit = 3; // 3 offres par page maximum
        $offset = ($page - 1) * $limit; // Calcul du décalage

        // 3. On récupère les 3 offres de la page actuelle ET le nombre total
        $offres = \App\Models\Offre::getPaginated($keyword, $location, $limit, $offset);
        $totalOffres = \App\Models\Offre::countPaginated($keyword, $location);
        $totalPages = ceil($totalOffres / $limit); // Arrondi au supérieur (ex: 7 offres = 3 pages)

        // 4. Gestion des favoris
        $mesFavorisIds = [];
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 5. Statistiques
        $stats = \App\Models\Stats::getDashboardStats();

        // 6. On envoie tout à la vue !
        echo $this->twig->render('accueil.html.twig', [
            'offres' => $offres,
            'mesFavorisIds' => $mesFavorisIds,
            'stats' => $stats,
            'search_q' => $keyword,
            'search_lieu' => $location,
            'currentPage' => $page,
            'totalPages' => $totalPages // On envoie le nombre de pages à Twig
        ]);
    }
}