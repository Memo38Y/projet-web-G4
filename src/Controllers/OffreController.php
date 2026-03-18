<?php

namespace App\Controllers;

class OffreController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function show()
    {
        if (!isset($_GET['id'])) {
            header('Location: /');
            exit;
        }

        $id = (int)$_GET['id'];

        // 1. On récupère les détails de l'offre et de l'entreprise
        $offre = \App\Models\Offre::getById($id);
        
        if (!$offre) {
            header('Location: /');
            exit;
        }

        // 2. On récupère les compétences
        $competences = \App\Models\Offre::getCompetences($id);

        // 3. On vérifie si l'étudiant a déjà mis cette offre en favori
        $estFavori = false;
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1) {
            $offresFavorites = \App\Models\Favori::getUserFavorites($_SESSION['user']['id']);
            $mesFavorisIds = array_column($offresFavorites, 'Id_OFFRE');
            // in_array vérifie si l'ID de la page actuelle est dans la liste des favoris
            $estFavori = in_array($id, $mesFavorisIds); 
        }

        // 4. On envoie tout à la vue Twig
        echo $this->twig->render('offre_detail.html.twig', [
            'offre' => $offre,
            'competences' => $competences,
            'estFavori' => $estFavori
        ]);
    }
}