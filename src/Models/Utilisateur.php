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
}