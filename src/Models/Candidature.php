<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Candidature 
{
    /**
     * Enregistre une nouvelle candidature dans la base de données
     */
    public static function save($student_id, $offre_id, $cv_path, $motivation_text) 
    {
        $db = Database::getInstance();
        
        // On utilise tes colonnes exactes. CURDATE() met la date du jour automatiquement.
        $sql = "INSERT INTO Postuler (Id_Utilisateur, Id_OFFRE, cv_path, lm_path, date_candidature) 
                VALUES (?, ?, ?, ?, CURDATE())";
        
        $stmt = $db->prepare($sql);
        return $stmt->execute([$student_id, $offre_id, $cv_path, $motivation_text]);
    }

    /**
     * Récupère toutes les candidatures (pour le panel Admin/Pilote)
     */
    public static function getAll() 
    {
        $db = Database::getInstance();
        $sql = "SELECT Postuler.*, Utilisateur.nom, Utilisateur.prenom, OFFRE.titre 
                FROM Postuler
                JOIN Utilisateur ON Postuler.Id_Utilisateur = Utilisateur.Id_Utilisateur
                JOIN OFFRE ON Postuler.Id_OFFRE = OFFRE.Id_OFFRE
                ORDER BY Postuler.date_postulat DESC";
                
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère toutes les candidatures d'un étudiant spécifique
     */
    public static function getByEtudiant($idEtudiant) 
    {
        $db = Database::getInstance();
        $sql = "SELECT Postuler.cv_path, Postuler.lm_path, OFFRE.Id_OFFRE, OFFRE.titre, OFFRE.lieu, ENTREPRISE.nom AS nom_entreprise 
                FROM Postuler
                JOIN OFFRE ON Postuler.Id_OFFRE = OFFRE.Id_OFFRE
                JOIN ENTREPRISE ON OFFRE.Id_ENTREPRISE = ENTREPRISE.Id_ENTREPRISE
                WHERE Postuler.Id_Utilisateur = ?";
                
        $req = $db->prepare($sql);
        $req->execute([$idEtudiant]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime la candidature d'un étudiant pour une offre précise
     */
    public static function delete($student_id, $offre_id) 
    {
        $db = Database::getInstance();
        $sql = "DELETE FROM Postuler WHERE Id_Utilisateur = ? AND Id_OFFRE = ?";
        $req = $db->prepare($sql);
        return $req->execute([$student_id, $offre_id]);
    }

    /**
     * Vérifie si un étudiant a déjà postulé à une offre spécifique
     */
    public static function hasApplied($student_id, $offre_id) 
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT 1 FROM Postuler WHERE Id_Utilisateur = ? AND Id_OFFRE = ?";
        $req = $db->prepare($sql);
        $req->execute([$student_id, $offre_id]);
        
        // S'il trouve une ligne, ça renvoie true, sinon false
        return $req->fetch() !== false;
    }

    /**
     * Récupère toutes les candidatures des étudiants rattachés à un pilote spécifique
     */
    public static function getByPilote($idPilote) 
    {
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT Postuler.cv_path, Postuler.lm_path, 
                       OFFRE.Id_OFFRE, OFFRE.titre, 
                       ENTREPRISE.nom AS nom_entreprise,
                       Utilisateur.Id_Utilisateur, Utilisateur.nom AS nom_etudiant, Utilisateur.prenom AS prenom_etudiant
                FROM Postuler
                JOIN OFFRE ON Postuler.Id_OFFRE = OFFRE.Id_OFFRE
                JOIN ENTREPRISE ON OFFRE.Id_ENTREPRISE = ENTREPRISE.Id_ENTREPRISE
                JOIN Utilisateur ON Postuler.Id_Utilisateur = Utilisateur.Id_Utilisateur
                WHERE Utilisateur.Id_pilote = ?
                ORDER BY Utilisateur.nom ASC, Utilisateur.prenom ASC";
                
        $req = $db->prepare($sql);
        $req->execute([$idPilote]);
        return $req->fetchAll(\PDO::FETCH_ASSOC);
    }
}