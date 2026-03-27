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
}