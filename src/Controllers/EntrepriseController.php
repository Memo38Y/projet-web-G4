<?php

namespace App\Controllers;

use App\Models\Entreprise;

class EntrepriseController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        // 1. On intercepte la recherche
        $keyword = $_GET['q'] ?? '';
        
        // 2. Gestion de la Pagination
        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $limit = 4; // On affiche 4 entreprises maximum par page
        $offset = ($page - 1) * $limit;

        // 3. On récupère les entreprises de la page actuelle ET le total
        $entreprises = \App\Models\Entreprise::getPaginated($keyword, $limit, $offset);
        $totalEntreprises = \App\Models\Entreprise::countPaginated($keyword);
        $totalPages = ceil($totalEntreprises / $limit);

        // 4. On envoie les données à la vue Twig
        echo $this->twig->render('entreprise.html.twig', [
            'entreprises' => $entreprises,
            'search_q' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function showOffres()
    {
        // 1. On vérifie qu'on a bien un ID dans l'URL
        if (!isset($_GET['id'])) {
            header('Location: /entreprises');
            exit;
        }

        $id = (int)$_GET['id'];

        // 2. On va chercher l'entreprise
        $entreprise = \App\Models\Entreprise::getById($id);
        
        // Si elle n'existe pas, on redirige vers la liste
        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        // 3. NOUVEAU : Recherche et Pagination des offres de cette entreprise
        $keyword = $_GET['q'] ?? '';
        $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
        $limit = 3; // 3 offres par page maximum
        $offset = ($page - 1) * $limit;

        $offres = \App\Models\Offre::getPaginatedByEntreprise($id, $keyword, $limit, $offset);
        $totalOffres = \App\Models\Offre::countPaginatedByEntreprise($id, $keyword);
        $totalPages = ceil($totalOffres / $limit);

        // 4. Gestion des favoris pour l'affichage des drapeaux bleus
        $mesFavorisIds = [];

        // Si c'est un étudiant connecté, on récupère les ID de ses favoris
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            // On extrait juste les numéros d'offres pour faire une liste simple
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 5. On vérifie si l'utilisateur connecté a déjà évalué cette entreprise
        $monEvaluation = null;
        if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], [2, 3])) {
            $monEvaluation = \App\Models\Entreprise::getEvaluation($_SESSION['user']['id'], $id);
        }

        // 6. NOUVEAU : On récupère toutes les évaluations publiques de cette entreprise
        $evaluationsEntreprise = \App\Models\Entreprise::getEvaluationsByEntreprise($id);

        // 7. On envoie absolument toutes les données à la page Twig
        echo $this->twig->render('entreprise_offres.html.twig', [
            'entreprise' => $entreprise,
            'offres' => $offres,
            'mesFavorisIds' => $mesFavorisIds,
            'monEvaluation' => $monEvaluation,
            'evaluationsEntreprise' => $evaluationsEntreprise,
            // Variables de pagination et recherche :
            'search_q' => $keyword,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    // NOUVELLE FONCTION : Gère l'ajout, la modification et la suppression de l'évaluation
    public function gererEvaluation()
    {
        // Sécurité : Uniquement Pilotes (2) et Admins (3)
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [2, 3])) {
            header('Location: /');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idEntreprise = $_POST['id_entreprise'] ?? null;
            $action = $_POST['action'] ?? '';
            $idUser = $_SESSION['user']['id'];

            if ($idEntreprise) {
                // Si on ajoute ou modifie
                if ($action === 'save') {
                    $note = (int)($_POST['note'] ?? 0);
                    
                    if ($note >= 1 && $note <= 5) {
                        \App\Models\Entreprise::saveEvaluation($idUser, $idEntreprise, $note);
                    }
                } 
                // Si on supprime
                elseif ($action === 'delete') {
                    \App\Models\Entreprise::deleteEvaluation($idUser, $idEntreprise);
                }
                
                // On redirige vers la page de l'entreprise d'où l'on vient
                $redirectUrl = $_SERVER['HTTP_REFERER'] ?? '/entreprises';
                header("Location: " . $redirectUrl);
                exit;
            }
        }
    }
}