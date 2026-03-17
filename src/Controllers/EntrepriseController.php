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
        // On vérifie qu'on a bien un ID dans l'URL
        if (!isset($_GET['id'])) {
            header('Location: /entreprises');
            exit;
        }

        $id = (int)$_GET['id'];

        // 1. On va chercher l'entreprise
        $entreprise = \App\Models\Entreprise::getById($id);
        
        // Si elle n'existe pas, on redirige
        if (!$entreprise) {
            header('Location: /entreprises');
            exit;
        }

        // 2. On va chercher ses offres
        $offres = \App\Models\Offre::getByEntreprise($id);

        // 3. On envoie tout ça à notre nouvelle page Twig
        echo $this->twig->render('entreprise_offres.html.twig', [
            'entreprise' => $entreprise,
            'offres' => $offres
        ]);
    }
}