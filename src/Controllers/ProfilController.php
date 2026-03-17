<?php
namespace App\Controllers;

use App\Models\Favori; // <-- N'oublie pas l'import !

class ProfilController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /connexion');
            exit;
        }

        // Si c'est un étudiant (rôle 1), on va chercher ses favoris
        $mesFavoris = [];
        if ($_SESSION['user']['role'] == 1) {
            $mesFavoris = Favori::getUserFavorites($_SESSION['user']['id']);
        }

        // On envoie la variable à Twig
        echo $this->twig->render('profil.html.twig', [
            'favoris' => $mesFavoris
        ]);
    }
}