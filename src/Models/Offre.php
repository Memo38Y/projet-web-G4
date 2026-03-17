<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Offre
{
    /**
     * Récupère les 3 dernières offres ajoutées dans MariaDB
     */
    public static function getLastThree(): array
    {
        // 1. On récupère notre connexion PDO
        $db = Database::getInstance();

        // 2. On exécute la requête SQL
        $requete = $db->query("SELECT * FROM OFFRE ORDER BY Id_OFFRE DESC LIMIT 3");

        // 3. On retourne les résultats sous forme de tableau associatif
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupère toutes les offres d'une entreprise spécifique
    public static function getByEntreprise($idEntreprise)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM OFFRE WHERE Id_ENTREPRISE = ? ORDER BY date_offre DESC");
        $req->execute([$idEntreprise]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}