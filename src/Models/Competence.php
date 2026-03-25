<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Competence
{
    /**
     * Récupère toutes les compétences par ordre alphabétique
     */
    public static function getAll()
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM COMPETENCE ORDER BY libelle ASC");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle compétence
     */
    public static function create($libelle)
    {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO COMPETENCE (libelle) VALUES (?)");
        return $req->execute([$libelle]);
    }

    /**
     * Supprime une compétence (et nettoie les liens avec les offres)
     */
    public static function delete($id)
    {
        $db = Database::getInstance();
        
        // 1. On supprime d'abord les liens dans la table 'Requerir' pour ne pas bloquer MariaDB
        $db->prepare("DELETE FROM Requerir WHERE Id_COMPETENCE = ?")->execute([$id]);
        
        // 2. On supprime la compétence
        $req = $db->prepare("DELETE FROM COMPETENCE WHERE Id_COMPETENCE = ?");
        return $req->execute([$id]);
    }

    /**
     * Met à jour les compétences d'une offre (supprime les anciennes et insère les nouvelles)
     */
    public static function syncForOffre($idOffre, $competencesIds)
    {
        $db = Database::getInstance();
        
        // 1. On nettoie l'ardoise : on supprime toutes les compétences actuelles de cette offre
        $db->prepare("DELETE FROM Requerir WHERE Id_OFFRE = ?")->execute([$idOffre]);

        // 2. On insère les nouvelles cases qui ont été cochées
        if (!empty($competencesIds)) {
            $sql = "INSERT INTO Requerir (Id_OFFRE, Id_COMPETENCE) VALUES (?, ?)";
            $stmt = $db->prepare($sql);
            foreach ($competencesIds as $idComp) {
                $stmt->execute([$idOffre, $idComp]);
            }
        }
    }

    /**
     * Récupère un tableau de toutes les liaisons Offres/Compétences pour le JavaScript
     */
    public static function getAllRelations()
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT Id_OFFRE, Id_COMPETENCE FROM Requerir");
        
        $relations = [];
        foreach($req->fetchAll(PDO::FETCH_ASSOC) as $row) {
            // On range les ID de compétences dans un tiroir portant le numéro de l'offre
            $relations[$row['Id_OFFRE']][] = $row['Id_COMPETENCE'];
        }
        return $relations;
    }
}