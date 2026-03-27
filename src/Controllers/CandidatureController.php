<?php

namespace App\Controllers;

use App\Models\Candidature;
use Throwable;

class CandidatureController 
{
    private $twig;

    public function __construct($twig) 
    {
        $this->twig = $twig;
    }

    /**
     * Traite l'envoi du formulaire de candidature
     */
    public function submit()
    {
        try {
            // 1. Sécurité : Seul un Étudiant (Rôle 1) peut postuler
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
                header('Location: /connexion');
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $student_id = $_SESSION['user']['id'];
                $offre_id = $_POST['id_offre'] ?? null;
                $motivation = $_POST['motivation'] ?? '';
                $cv = $_FILES['cv'] ?? null;

                if ($offre_id && $cv && $cv['error'] === 0) {
                    
                    // 2. Vérification PDF
                    $ext = pathinfo($cv['name'], PATHINFO_EXTENSION);
                    if (strtolower($ext) != "pdf") {
                        die("<div style='background:#ffcccc; padding:20px; color:red;'>Erreur : Le CV doit être au format PDF.</div>");
                    }

                    // 3. Dossier d'upload (Création du chemin absolu robuste)
                    // On se place dans le dossier /public/uploads/cvs/
                    $folder = dirname(__DIR__, 2) . "/public/uploads/cvs/";
                    
                    if (!is_dir($folder)) {
                        // On crée le dossier s'il n'existe pas avec les droits d'écriture
                        if (!mkdir($folder, 0777, true)) {
                            die("<div style='background:#ffcccc; padding:20px; color:red;'>Erreur 500 : Impossible de créer le dossier d'upload. Vérifie les droits du dossier public/.</div>");
                        }
                    }

                    // 4. Nom unique et déplacement du fichier
                    $cvName = uniqid('cv_') . ".pdf";
                    $absolutePath = $folder . $cvName;

                    if (move_uploaded_file($cv['tmp_name'], $absolutePath)) {
                        
                        // 5. On sauvegarde en BDD avec le chemin relatif
                        $dbPath = "/uploads/cvs/" . $cvName;
                        
                        Candidature::save($student_id, $offre_id, $dbPath, $motivation);
                        
                        // 6. Succès : on retourne sur l'offre (tu peux gérer le ?success=1 dans Twig plus tard si tu veux afficher un pop-up)
                        header("Location: /offre?id=" . $offre_id . "&success=1");
                        exit;
                        
                    } else {
                        die("<div style='background:#ffcccc; padding:20px; color:red;'>Erreur 500 : Le serveur a refusé de déplacer le fichier uploadé.</div>");
                    }
                } else {
                    die("<div style='background:#ffcccc; padding:20px; color:red;'>Erreur : Formulaire incomplet ou fichier corrompu.</div>");
                }
            }
        } catch (Throwable $e) {
            // FINI LES ERREURS 500 SILENCIEUSES !
            die("<div style='background:#ffcccc; padding:20px; color:red; font-family:sans-serif;'>
                    <h2>🚨 ERREUR SERVEUR 🚨</h2>
                    <p><strong>Message :</strong> " . $e->getMessage() . "</p>
                    <p><strong>Fichier :</strong> " . $e->getFile() . " (Ligne " . $e->getLine() . ")</p>
                 </div>");
        }
    }
}