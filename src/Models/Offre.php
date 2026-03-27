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

    /**
     * Récupère TOUTES les offres avec le nom de l'entreprise associée (pour la liste admin)
     */
    public static function getAll()
    {
        $db = Database::getInstance();
        $sql = "SELECT OFFRE.*, ENTREPRISE.nom AS nom_entreprise 
                FROM OFFRE 
                JOIN ENTREPRISE ON OFFRE.Id_ENTREPRISE = ENTREPRISE.Id_ENTREPRISE 
                ORDER BY date_offre DESC";
        $req = $db->query($sql);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle offre (la date se met automatiquement à aujourd'hui avec CURDATE())
     */
    public static function create($titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat)
    {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO OFFRE (titre, description, base_remuneration, date_offre, Id_ENTREPRISE, lieu, type_contrat) 
                             VALUES (?, ?, ?, CURDATE(), ?, ?, ?)");
        return $req->execute([$titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat]);
    }

    /**
     * Met à jour une offre existante
     */
    public static function update($id, $titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat)
    {
        $db = Database::getInstance();
        $req = $db->prepare("UPDATE OFFRE SET titre = ?, description = ?, base_remuneration = ?, Id_ENTREPRISE = ?, lieu = ?, type_contrat = ? 
                             WHERE Id_OFFRE = ?");
        return $req->execute([$titre, $description, $remuneration, $idEntreprise, $lieu, $typeContrat, $id]);
    }

    /**
     * Supprime une offre ET nettoie tout ce qui lui est lié en cascade
     */
    public static function delete($id)
    {
        $db = \App\Core\Database::getInstance();
        
        // 1. On supprime toutes les candidatures liées à cette offre
        $db->prepare("DELETE FROM Postuler WHERE Id_OFFRE = ?")->execute([$id]);
        
        // 2. On supprime tous les favoris liés à cette offre
        $db->prepare("DELETE FROM Mettre_Favori WHERE Id_OFFRE = ?")->execute([$id]);
        
        // 3. On détache toutes les compétences requises pour cette offre (LE COUPABLE ÉTAIT ICI !)
        $db->prepare("DELETE FROM Requerir WHERE Id_OFFRE = ?")->execute([$id]);
        
        // 4. L'offre est enfin isolée, on peut la supprimer de la table principale !
        $req = $db->prepare("DELETE FROM OFFRE WHERE Id_OFFRE = ?");
        return $req->execute([$id]);
    }
}