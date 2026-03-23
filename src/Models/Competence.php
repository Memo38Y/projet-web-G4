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
}