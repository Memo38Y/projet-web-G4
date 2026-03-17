<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Favori
{
    // Ajoute ou retire un favori (fonctionnement on/off)
    public static function toggle($idUser, $idOffre)
    {
        $db = Database::getInstance();
        
        // On regarde si le favori existe déjà
        $check = $db->prepare("SELECT * FROM Mettre_Favori WHERE Id_Utilisateur = ? AND Id_OFFRE = ?");
        $check->execute([$idUser, $idOffre]);
        
        if ($check->fetch()) {
            // S'il existe, on l'enlève
            $del = $db->prepare("DELETE FROM Mettre_Favori WHERE Id_Utilisateur = ? AND Id_OFFRE = ?");
            $del->execute([$idUser, $idOffre]);
            return 'removed';
        } else {
            // S'il n'existe pas, on l'ajoute
            $ins = $db->prepare("INSERT INTO Mettre_Favori (Id_Utilisateur, Id_OFFRE, date_ajout) VALUES (?, ?, NOW())");
            $ins->execute([$idUser, $idOffre]);
            return 'added';
        }
    }

    // Récupère les offres favorites d'un utilisateur
    public static function getUserFavorites($idUser)
    {
        $db = Database::getInstance();
        $sql = "SELECT OFFRE.* FROM OFFRE 
                JOIN Mettre_Favori ON OFFRE.Id_OFFRE = Mettre_Favori.Id_OFFRE 
                WHERE Mettre_Favori.Id_Utilisateur = ?";
        $req = $db->prepare($sql);
        $req->execute([$idUser]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}