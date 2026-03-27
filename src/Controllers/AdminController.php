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

    public function gererPilotes()
    {
        // VIGILE Admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 3) {
            header('Location: /');
            exit;
        }

        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // 1. Action : CRÉATION
            if (isset($_POST['action_create'])) {
                $nom = $_POST['nom'] ?? '';
                $prenom = $_POST['prenom'] ?? '';
                $email = $_POST['email'] ?? '';
                $mdp = $_POST['password'] ?? '';

                if ($nom && $prenom && $email && $mdp) {
                    if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                        $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                    } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || strlen($mdp) > 50) {
                        $message = "❌ Erreur : Limite de 50 caractères dépassée.";
                    } else {
                        Utilisateur::createPilote($nom, $prenom, $email, $mdp);
                        $message = "✅ Pilote $prenom $nom créé !";
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

                if ($id && $nom && $prenom && $email) {
                    if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                        $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                    } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || (!empty($mdp) && strlen($mdp) > 50)) {
                        $message = "❌ Erreur : Limite de 50 caractères dépassée.";
                    } else {
                        Utilisateur::updatePilote($id, $nom, $prenom, $email, $mdp);
                        $message = "✅ Informations du pilote mises à jour !";
                    }
                }
            }
            
            // 3. Action : SUPPRESSION
            elseif (isset($_POST['action_delete'])) {
                $id = $_POST['id_utilisateur_edit'] ?? null;
                $nom = $_POST['nom_edit'] ?? '';
                $prenom = $_POST['prenom_edit'] ?? '';

                if ($id) {
                    try {
                        Utilisateur::deletePilote($id);
                        $message = "🗑️ Le pilote $prenom $nom a été supprimé. Ses anciens étudiants n'ont plus de pilote assigné.";
                    } catch (\PDOException $e) {
                        $message = "❌ Impossible de supprimer ce pilote.";
                    }
                }
            }
        }

        // On a déjà cette méthode dans notre modèle, parfait pour la liste !
        $pilotes = Utilisateur::getPilotes();

        echo $this->twig->render('admin_pilotes.html.twig', [
            'pilotes' => $pilotes,
            'message' => $message
        ]);
    }

    public function gererEntreprises()
    {
        try {
            if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [2, 3])) {
                header('Location: /');
                exit;
            }

            $message = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                // 1. CRÉATION
                if (isset($_POST['action_create'])) {
                    $nom = $_POST['nom'] ?? '';
                    $description = $_POST['description'] ?? '';
                    $email = $_POST['email_contact'] ?? '';
                    $tel = $_POST['tel_contact'] ?? '';
                    $secteur = $_POST['secteur_activite'] ?? '';

                    if ($nom && $email && $description) {
                        if (strlen($nom) > 100) {
                            $message = "❌ Erreur : Le nom de l'entreprise est trop long.";
                        } else {
                            \App\Models\Entreprise::create($nom, $description, $email, $tel, $secteur);
                            $message = "✅ L'entreprise $nom a été ajoutée !";
                        }
                    }
                }
                
                // 2. MODIFICATION
                elseif (isset($_POST['action_update'])) {
                    $id = $_POST['id_entreprise_edit'] ?? null;
                    $nom = $_POST['nom_edit'] ?? '';
                    $description = $_POST['description_edit'] ?? '';
                    $email = $_POST['email_edit'] ?? '';
                    $tel = $_POST['tel_edit'] ?? '';
                    $secteur = $_POST['secteur_edit'] ?? '';

                    if ($id && $nom && $email) {
                        if (strlen($nom) > 100) {
                            $message = "❌ Erreur : Le nom de l'entreprise est trop long.";
                        } else {
                            \App\Models\Entreprise::update($id, $nom, $description, $email, $tel, $secteur);
                            $message = "✅ Informations de l'entreprise $nom mises à jour !";
                        }
                    }
                }
                
                // 3. SUPPRESSION
                elseif (isset($_POST['action_delete'])) {
                    $id = $_POST['id_entreprise_edit'] ?? null;
                    $nom = $_POST['nom_edit'] ?? '';

                    if ($id) {
                        try {
                            \App\Models\Entreprise::delete($id);
                            $message = "🗑️ L'entreprise $nom a été supprimée.";
                        } catch (\PDOException $e) {
                            $message = "❌ Impossible de supprimer cette entreprise car elle a déjà publié des offres de stage.";
                        }
                    }
                }
            }

            $entreprises = \App\Models\Entreprise::getAll();

            echo $this->twig->render('admin_entreprises.html.twig', [
                'entreprises' => $entreprises,
                'message' => $message
            ]);

        // 🚨 SI UNE ERREUR ARRIVE, ON L'AFFICHE EN ROUGE 🚨
        } catch (\Throwable $e) {
            die("<div style='background:#ffcccc; padding:20px; color:red; font-family:sans-serif;'>
                    <h2>🚨 ALERTE ROUGE : ERREUR DÉTECTÉE 🚨</h2>
                    <p><strong>Message :</strong> " . $e->getMessage() . "</p>
                    <p><strong>Fichier :</strong> " . $e->getFile() . "</p>
                    <p><strong>Ligne :</strong> " . $e->getLine() . "</p>
                 </div>");
        }
    }

    public function gererOffres()
    {
        try {
            if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [2, 3])) {
                header('Location: /');
                exit;
            }

            $message = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                // 1. CRÉATION
                if (isset($_POST['action_create'])) {
                    $titre = $_POST['titre'] ?? '';
                    $idEntreprise = $_POST['id_entreprise'] ?? '';
                    $lieu = $_POST['lieu'] ?? '';
                    $typeContrat = $_POST['type_contrat'] ?? '';
                    $remuneration = !empty($_POST['remuneration']) ? $_POST['remuneration'] : 0;
                    $description = $_POST['description'] ?? '';

                    if ($titre && $idEntreprise && $lieu && $typeContrat) {
                        \App\Models\Offre::create($titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat);
                        $message = "✅ L'offre '$titre' a été publiée !";
                    }
                }
                
                // 2. MODIFICATION
                elseif (isset($_POST['action_update'])) {
                    $id = $_POST['id_offre_edit'] ?? null;
                    $titre = $_POST['titre_edit'] ?? '';
                    $idEntreprise = $_POST['id_entreprise_edit'] ?? '';
                    $lieu = $_POST['lieu_edit'] ?? '';
                    $typeContrat = $_POST['type_contrat_edit'] ?? '';
                    $remuneration = !empty($_POST['remuneration_edit']) ? $_POST['remuneration_edit'] : 0;
                    $description = $_POST['description_edit'] ?? '';

                    if ($id && $titre && $idEntreprise) {
                        \App\Models\Offre::update($id, $titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat);
                        $message = "✅ L'offre '$titre' a été mise à jour !";
                    }
                }
                
                // 3. SUPPRESSION
                elseif (isset($_POST['action_delete'])) {
                    $id = $_POST['id_offre_edit'] ?? null;
                    $titre = $_POST['titre_edit'] ?? '';

                    if ($id) {
                        try {
                            \App\Models\Offre::delete($id);
                            $message = "🗑️ L'offre '$titre' a été supprimée.";
                        } catch (\PDOException $e) {
                            $message = "❌ Impossible de supprimer cette offre (des étudiants y ont peut-être déjà postulé).";
                        }
                    }
                }
            }

            // On récupère les offres ET les entreprises pour le menu déroulant
            $offres = \App\Models\Offre::getAll();
            $entreprises = \App\Models\Entreprise::getAll();

            echo $this->twig->render('admin_offres.html.twig', [
                'offres' => $offres,
                'entreprises' => $entreprises,
                'message' => $message
            ]);

        } catch (\Throwable $e) {
            die("<div style='background:#ffcccc; padding:20px; color:red; font-family:sans-serif;'>
                    <h2>🚨 ALERTE ROUGE : ERREUR DÉTECTÉE 🚨</h2>
                    <p><strong>Message :</strong> " . $e->getMessage() . "</p>
                    <p><strong>Fichier :</strong> " . $e->getFile() . "</p>
                    <p><strong>Ligne :</strong> " . $e->getLine() . "</p>
                 </div>");
        }
    }

    public function gererCompetences()
    {
        try {
            // VIGILE Admin (Rôle 3)
            if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], [2, 3])) {
                header('Location: /');
                exit;
            }

            $message = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // 1. CRÉATION
                if (isset($_POST['action_create'])) {
                    $libelle = trim($_POST['libelle'] ?? '');
                    if ($libelle) {
                        \App\Models\Competence::create($libelle);
                        $message = "✅ La compétence '$libelle' a été ajoutée au dictionnaire !";
                    }
                }
                // 2. SUPPRESSION
                elseif (isset($_POST['action_delete'])) {
                    $id = $_POST['id_competence'] ?? null;
                    if ($id) {
                        \App\Models\Competence::delete($id);
                        $message = "🗑️ La compétence a été supprimée définitivement.";
                    }
                }
                // 3. SYNCHRONISATION AVEC UNE OFFRE
                elseif (isset($_POST['action_sync'])) {
                    $idOffre = $_POST['id_offre'] ?? null;
                    $competencesChoisies = $_POST['competences'] ?? []; // Tableau des cases cochées

                    if ($idOffre) {
                        \App\Models\Competence::syncForOffre($idOffre, $competencesChoisies);
                        $message = "🔗 Les compétences de l'offre ont été mises à jour avec succès !";
                    }
                }
            }

            // On prépare toutes les données pour la vue
            $competences = \App\Models\Competence::getAll();
            $offres = \App\Models\Offre::getAll();
            $relations = \App\Models\Competence::getAllRelations();

            echo $this->twig->render('admin_competences.html.twig', [
                'competences' => $competences,
                'offres' => $offres,
                // On transforme le tableau PHP en texte JSON pour que JavaScript puisse le lire facilement
                'relations' => json_encode($relations),
                'message' => $message
            ]);

        } catch (\Throwable $e) {
            die("<div style='background:#ffcccc; padding:20px; color:red;'>
                    <h2>🚨 ERREUR COMPÉTENCES 🚨</h2>
                    <p>" . $e->getMessage() . "</p>
                 </div>");
        }
    }

    public function gererMesEtudiants()
    {
        try {
            // VIGILE : STRICTEMENT RÉSERVÉ AUX PILOTES (Rôle 2)
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
                header('Location: /');
                exit;
            }

            $message = null;
            // On récupère l'ID du Pilote connecté !
            $monIdPilote = $_SESSION['user']['id']; 

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                // 1. CRÉATION
                if (isset($_POST['action_create'])) {
                    $nom = $_POST['nom'] ?? '';
                    $prenom = $_POST['prenom'] ?? '';
                    $email = $_POST['email'] ?? '';
                    $mdp = $_POST['password'] ?? '';

                    if ($nom && $prenom && $email && $mdp) {
                        if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                            $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                        } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || strlen($mdp) > 50) {
                            $message = "❌ Erreur : Limite de 50 caractères dépassée.";
                        } else {
                            // On force l'ID du pilote connecté lors de la création !
                            \App\Models\Utilisateur::createEtudiant($nom, $prenom, $email, $mdp, $monIdPilote);
                            $message = "✅ Ton étudiant $prenom $nom a été créé et t'a été assigné !";
                        }
                    }
                }
                
                // 2. MODIFICATION
                elseif (isset($_POST['action_update'])) {
                    $id = $_POST['id_utilisateur_edit'] ?? null;
                    $nom = $_POST['nom_edit'] ?? '';
                    $prenom = $_POST['prenom_edit'] ?? '';
                    $email = $_POST['email_edit'] ?? '';
                    $mdp = $_POST['password_edit'] ?? null;

                    if ($id && $nom && $prenom && $email) {
                        if (preg_match('/\d/', $nom) || preg_match('/\d/', $prenom)) {
                            $message = "❌ Erreur : Le nom et le prénom ne doivent pas contenir de chiffres.";
                        } elseif (strlen($nom) > 50 || strlen($prenom) > 50 || (!empty($mdp) && strlen($mdp) > 50)) {
                            $message = "❌ Erreur : Limite de 50 caractères dépassée.";
                        } else {
                            // On force l'ID du pilote connecté pour s'assurer qu'il reste son tuteur
                            \App\Models\Utilisateur::updateEtudiant($id, $nom, $prenom, $email, $mdp, $monIdPilote);
                            $message = "✅ Informations de $prenom mises à jour !";
                        }
                    }
                }
                
                // 3. SUPPRESSION
                elseif (isset($_POST['action_delete'])) {
                    $id = $_POST['id_utilisateur_edit'] ?? null;
                    $prenom = $_POST['prenom_edit'] ?? '';

                    if ($id) {
                        try {
                            \App\Models\Utilisateur::deleteEtudiant($id);
                            $message = "🗑️ L'étudiant $prenom a été supprimé de ta promotion.";
                        } catch (\PDOException $e) {
                            $message = "❌ Impossible de supprimer cet étudiant (candidatures en cours).";
                        }
                    }
                }
            }

            // On récupère UNIQUEMENT les étudiants de ce pilote
            $mesEtudiants = \App\Models\Utilisateur::getEtudiantsByPilote($monIdPilote);

            $candidaturesPromo = [];
            if (isset($_SESSION['user'])) {
                $candidaturesPromo = \App\Models\Candidature::getByPilote($_SESSION['user']['id']);
            }

            echo $this->twig->render('pilote_etudiants.html.twig', [
                'etudiants' => $mesEtudiants,
                'message' => $message,
                'candidaturesPromo' => $candidaturesPromo
            ]);

        } catch (\Throwable $e) {
            die("<div style='background:#ffcccc; padding:20px; color:red;'>
                    <h2>🚨 ERREUR 🚨</h2><p>" . $e->getMessage() . "</p>
                 </div>");
        }
    }
}