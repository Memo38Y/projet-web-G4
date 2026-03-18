<?php

namespace App\Controllers;

use App\Models\Utilisateur;

class AdminController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function gererEtudiants()
    {
        // LE VIGILE : Si ce n'est pas un Admin (rôle 3), on le jette !
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 3) {
            header('Location: /');
            exit;
        }

        $message = null;

        // Si l'Admin a validé le formulaire de création
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['password'] ?? '';
            // Si l'admin n'a pas choisi de pilote, on met NULL
            $idPilote = !empty($_POST['id_pilote']) ? $_POST['id_pilote'] : null;

            if ($nom && $prenom && $email && $mdp) {
                // On crée l'étudiant via le Modèle
                Utilisateur::createEtudiant($nom, $prenom, $email, $mdp, $idPilote);
                $message = "✅ L'étudiant $prenom $nom a été créé avec succès !";
            } else {
                $message = "❌ Veuillez remplir tous les champs obligatoires.";
            }
        }

        // On récupère la liste des pilotes pour le menu déroulant du formulaire
        $pilotes = Utilisateur::getPilotes();

        // On affiche la vue
        echo $this->twig->render('admin_etudiants.html.twig', [
            'pilotes' => $pilotes,
            'message' => $message
        ]);
    }
}