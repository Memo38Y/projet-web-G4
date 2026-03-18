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
        return $req->execute([$nom, $prenom, $email, $mdp, $idPilote]);
    }
}