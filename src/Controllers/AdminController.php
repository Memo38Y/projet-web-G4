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
        // VIGILE Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != "3") {
            header('Location: /');
            exit;
        }

        $message = null;

        // --- TRAITEMENT DES FORMULAIRES (POST) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Action : CRÉATION
            if (isset($_POST['action_create'])) {
                $nom = $_POST['nom'] ?? '';
                $prenom = $_POST['prenom'] ?? '';
                $email = $_POST['email'] ?? '';
                $mdp = $_POST['password'] ?? '';
                $idPilote = !empty($_POST['id_pilote']) ? $_POST['id_pilote'] : null;

                if ($nom && $prenom && $email && $mdp) {
                    // VERIFICATIONS DE SÉCURITÉ
                    if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                        $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                    } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || strlen($mdp) > 50) {
                        $message = "❌ Erreur : Le nom, le prénom et le mot de passe sont limités à 50 caractères.";
                    } else {
                        Utilisateur::createEtudiant($nom, $prenom, $email, $mdp, $idPilote);
                        $message = "✅ Étudiant $prenom $nom créé !";
                    }
                }
            }
            
            // 2. Action : MODIFICATION
            elseif (isset($_POST['action_update'])) {
                $id = $_POST['id_utilisateur_edit'] ?? null;
                $nom = $_POST['nom_edit'] ?? '';
                $prenom = $_POST['prenom_edit'] ?? '';
                $email = $_POST['email_edit'] ?? '';
                $mdp = $_POST['password_edit'] ?? null;
                $idPilote = !empty($_POST['id_pilote_edit']) ? $_POST['id_pilote_edit'] : null;

                if ($id && $nom && $prenom && $email) {
                    // VERIFICATIONS DE SÉCURITÉ
                    if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                        $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                    } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || (!empty($mdp) && strlen($mdp) > 50)) {
                        $message = "❌ Erreur : Le nom, le prénom et le mot de passe sont limités à 50 caractères.";
                    } else {
                        Utilisateur::updateEtudiant($id, $nom, $prenom, $email, $mdp, $idPilote);
                        $message = "✅ Informations de $prenom $nom mises à jour !";
                    }
                }
            }
            
            // 3. Action : SUPPRESSION (inchangé)
            elseif (isset($_POST['action_delete'])) {
                // ... (Garde ton code de suppression actuel ici) ...
                $id = $_POST['id_utilisateur_edit'] ?? null;
                $nom = $_POST['nom_edit'] ?? '';
                $prenom = $_POST['prenom_edit'] ?? '';

                if ($id) {
                    try {
                        Utilisateur::deleteEtudiant($id);
                        $message = "🗑️ L'étudiant $prenom $nom a été supprimé.";
                    } catch (\PDOException $e) {
                        $message = "❌ Impossible de supprimer cet étudiant (il a probablement des candidatures ou favoris liés).";
                    }
                }
            }
        }

        // --- PRÉPARATION DES DONNÉES POUR LA VUE (GET) ---
        $pilotes = Utilisateur::getPilotes();
        $etudiants = Utilisateur::getAllEtudiants(); // Nouvelle liste pour la sélection

        echo $this->twig->render('admin_etudiants.html.twig', [
            'pilotes' => $pilotes,
            'etudiants' => $etudiants, // On envoie la liste des étudiants
            'message' => $message
        ]);
    }
}