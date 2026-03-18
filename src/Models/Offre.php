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
        $db = Database::getInstance();
        $requete = $db->query("SELECT * FROM OFFRE ORDER BY Id_OFFRE DESC LIMIT 3");
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = Database::getInstance();
        
        // On a retiré ENTREPRISE.logo_path qui faisait sûrement crasher la requête !
        $sql = "SELECT OFFRE.*, ENTREPRISE.nom AS nom_entreprise, ENTREPRISE.secteur_activite 
                FROM OFFRE 
                JOIN ENTREPRISE ON OFFRE.Id_ENTREPRISE = ENTREPRISE.Id_ENTREPRISE 
                WHERE Id_OFFRE = :id";
                
        $requete = $db->prepare($sql);
        $requete->execute(['id' => $id]);
        return $requete->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les offres d'une entreprise spécifique
     */
    public static function getByEntreprise($idEntreprise)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM OFFRE WHERE Id_ENTREPRISE = ? ORDER BY date_offre DESC");
        $req->execute([$idEntreprise]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste des compétences requises pour une offre
     */
    public static function getCompetences($idOffre)
    {
        $db = Database::getInstance();
        $sql = "SELECT COMPETENCE.libelle 
                FROM COMPETENCE 
                JOIN Requerir ON COMPETENCE.Id_COMPETENCE = Requerir.Id_COMPETENCE 
                WHERE Requerir.Id_OFFRE = ?";
        
        $req = $db->prepare($sql);
        $req->execute([$idOffre]);
        return $req->fetchAll(PDO::FETCH_COLUMN);
    }
}