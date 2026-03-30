<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Utilisateur
{
    /**
     * Cherche un utilisateur par son email
     */
    public static function findByEmail($email)
    {
        $db = Database::getInstance();
        
        $requete = $db->prepare("SELECT * FROM Utilisateur WHERE email = :email");
        $requete->execute(['email' => $email]);
        
        // Retourne les données de l'utilisateur, ou false s'il n'existe pas
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les utilisateurs qui ont le rôle de Pilote (Id_Role = 2)
     */
    public static function getPilotes()
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT Id_Utilisateur, nom, prenom FROM Utilisateur WHERE Id_Role = 2");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel étudiant (Rôle 1 forcé)
     */
    public static function createEtudiant($nom, $prenom, $email, $mdp, $idPilote)
    {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, Id_Role, Id_pilote) 
                         VALUES (?, ?, ?, ?, 1, ?)");
                         
        // On hache le mot de passe avant de l'exécuter
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
    
        return $req->execute([$nom, $prenom, $email, $mdpHash, $idPilote]);
    }

    /**
     * Récupère TOUS les étudiants (Rôle 1)
     */
    public static function getAllEtudiants()
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM Utilisateur WHERE Id_Role = 1 ORDER BY nom ASC");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour un étudiant existant
     */
    public static function updateEtudiant($id, $nom, $prenom, $email, $mdp = null, $idPilote = null)
    {
        $db = Database::getInstance();
        
        // Si un nouveau mot de passe est fourni, on l'inclut dans la requête
        if (!empty($mdp)) {
            $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, email = ?, mot_de_passe = ?, Id_pilote = ? 
                    WHERE Id_Utilisateur = ? AND Id_Role = 1";

            $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
            $params = [$nom, $prenom, $email, $mdp, $idPilote, $id];
        } else {
            // Sinon, on ne touche pas au mot de passe actuel
            $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, email = ?, Id_pilote = ? 
                    WHERE Id_Utilisateur = ? AND Id_Role = 1";
            $params = [$nom, $prenom, $email, $idPilote, $id];
        }
        
        $req = $db->prepare($sql);
        return $req->execute($params);
    }

    /**
     * Supprime un étudiant
     */
    public static function deleteEtudiant($id)
    {
        $db = Database::getInstance();
        // /!\ Attention : cela peut échouer si l'étudiant a des favoris ou candidatures (contraintes SQL)
        $req = $db->prepare("DELETE FROM Utilisateur WHERE Id_Utilisateur = ? AND Id_Role = 1");
        return $req->execute([$id]);
    }

    /**
     * Crée un nouveau Pilote (Rôle 2)
     */
    public static function createPilote($nom, $prenom, $email, $mdp)
    {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO Utilisateur (nom, prenom, email, mot_de_passe, Id_Role, Id_pilote) 
                         VALUES (?, ?, ?, ?, 2, NULL)");
                         
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
    
        return $req->execute([$nom, $prenom, $email, $mdpHash]);
    }

    /**
     * Met à jour un Pilote existant
     */
    public static function updatePilote($id, $nom, $prenom, $email, $mdp = null)
    {
        $db = Database::getInstance();
        
        if (!empty($mdp)) {
            $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, email = ?, mot_de_passe = ? 
                    WHERE Id_Utilisateur = ? AND Id_Role = 2";
            $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
            $params = [$nom, $prenom, $email, $mdp, $id];
        } else {
            $sql = "UPDATE Utilisateur SET nom = ?, prenom = ?, email = ? 
                    WHERE Id_Utilisateur = ? AND Id_Role = 2";
            $params = [$nom, $prenom, $email, $id];
        }
        
        $req = $db->prepare($sql);
        return $req->execute($params);
    }

    /**
     * Supprime un Pilote en gérant ses étudiants
     */
    public static function deletePilote($id)
    {
        $db = Database::getInstance();
        
        // 1. On "libère" les étudiants qui étaient sous la responsabilité de ce pilote
        $req1 = $db->prepare("UPDATE Utilisateur SET Id_pilote = NULL WHERE Id_pilote = ?");
        $req1->execute([$id]);
        
        // 2. On supprime le pilote
        $req2 = $db->prepare("DELETE FROM Utilisateur WHERE Id_Utilisateur = ? AND Id_Role = 2");
        return $req2->execute([$id]);
    }

    /**
     * Récupère uniquement les étudiants assignés à un Pilote spécifique
     */
    public static function getEtudiantsByPilote($idPilote)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM Utilisateur WHERE Id_Role = 1 AND Id_pilote = ? ORDER BY nom ASC");
        $req->execute([$idPilote]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}