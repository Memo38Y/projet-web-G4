<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Entreprise
{
    /**
     * Récupère toutes les entreprises de la base de données
     */
    public static function getAll(): array
    {
        $db = Database::getInstance();
        
        // On récupère tout, en triant par les plus récentes en premier
        $requete = $db->query("SELECT * FROM ENTREPRISE ORDER BY Id_ENTREPRISE DESC");
        
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupère les infos d'une seule entreprise
    public static function getById($id)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM ENTREPRISE WHERE Id_ENTREPRISE = ?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }
}