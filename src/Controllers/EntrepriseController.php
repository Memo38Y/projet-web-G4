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
        // On récupère la liste des entreprises depuis MariaDB
        $entreprises = Entreprise::getAll();

        // On envoie ces données à la vue Twig
        echo $this->twig->render('entreprise.html.twig', [
            'entreprises' => $entreprises
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

        // 3. On va chercher toutes ses offres
        $offres = \App\Models\Offre::getByEntreprise($id);

        // 4. Gestion des favoris pour l'affichage des drapeaux bleus
        $mesFavorisIds = [];

        // Si c'est un étudiant connecté, on récupère les ID de ses favoris
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            // On extrait juste les numéros d'offres pour faire une liste simple
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
        }

        // 5. On envoie absolument toutes les données à la page Twig
        echo $this->twig->render('entreprise_offres.html.twig', [
            'entreprise' => $entreprise,
            'offres' => $offres,
            'mesFavorisIds' => $mesFavorisIds
        ]);
    }
}