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
}