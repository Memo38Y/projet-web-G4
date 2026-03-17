<?php

namespace App\Controllers;

use App\Models\Utilisateur; // On n'oublie pas d'importer le modèle !

class AuthController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
    
    public function login()
    {
        $erreur = null;

        // Si le formulaire vient d'être soumis (clic sur le bouton)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // 1. On cherche l'utilisateur dans la BDD
            $user = Utilisateur::findByEmail($email);

            // 2. On vérifie s'il existe ET si le mot de passe correspond
            // (Note : on compare en texte brut car on n'a pas encore crypté les mots de passe dans notre jeu d'essai)
            if ($user && $user['mot_de_passe'] === $password) {
                
                // 3. SUCCÈS ! On crée son "badge" de session
                $_SESSION['user'] = [
                    'id' => $user['Id_Utilisateur'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'role' => $user['Id_Role']
                ];
                
                // 4. On le redirige vers l'accueil
                header('Location: /profil');
                exit;
            } else {
                // ÉCHEC : On prépare un message d'erreur
                $erreur = "Adresse email ou mot de passe incorrect.";
            }
        }

        // On affiche la page de connexion (en lui passant l'erreur éventuelle)
        echo $this->twig->render('connexion.html.twig', [
            'erreur' => $erreur
        ]);
    }
}