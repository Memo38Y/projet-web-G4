<?php

namespace App\Models;

use App\Core\Database;
use PDO; // 🚨 C'est souvent lui le coupable !

class Entreprise
{
    /**
     * Récupère les informations d'une seule entreprise par son ID
     */
    public static function getById($id)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM ENTREPRISE WHERE Id_ENTREPRISE = ?");
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère TOUTES les entreprises (pour la liste admin)
     */
    public static function getAll()
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM ENTREPRISE ORDER BY nom ASC");
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle entreprise
     */
    public static function create($nom, $description, $email, $tel, $secteur)
    {
        $db = Database::getInstance();
        $req = $db->prepare("INSERT INTO ENTREPRISE (nom, description, email_contact, tel_contact, secteur_activite) 
                             VALUES (?, ?, ?, ?, ?)");
        return $req->execute([$nom, $description, $email, $tel, $secteur]);
    }

    /**
     * Met à jour une entreprise existante
     */
    public static function update($id, $nom, $description, $email, $tel, $secteur)
    {
        $db = Database::getInstance();
        $req = $db->prepare("UPDATE ENTREPRISE SET nom = ?, description = ?, email_contact = ?, tel_contact = ?, secteur_activite = ? 
                             WHERE Id_ENTREPRISE = ?");
        return $req->execute([$nom, $description, $email, $tel, $secteur, $id]);
    }

    /**
     * Supprime une entreprise
     */
    public static function delete($id)
    {
        $db = Database::getInstance();
        $req = $db->prepare("DELETE FROM ENTREPRISE WHERE Id_ENTREPRISE = ?");
        return $req->execute([$id]);
    }

    /**
     * Récupère l'évaluation d'un utilisateur spécifique pour une entreprise
     */
    public static function getEvaluation($idUser, $idEntreprise)
    {
        $db = Database::getInstance();
        $req = $db->prepare("SELECT * FROM Evaluer WHERE Id_Utilisateur = ? AND Id_ENTREPRISE = ?");
        $req->execute([$idUser, $idEntreprise]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Sauvegarde (ajoute ou modifie) l'évaluation
     */
    public static function saveEvaluation($idUser, $idEntreprise, $note, $avis)
    {
        $db = Database::getInstance();
        // Le ON DUPLICATE KEY permet de mettre à jour si l'évaluation existe déjà !
        $sql = "INSERT INTO Evaluer (Id_Utilisateur, Id_ENTREPRISE, note, avis) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE note = VALUES(note), avis = VALUES(avis)";
        $req = $db->prepare($sql);
        return $req->execute([$idUser, $idEntreprise, $note, $avis]);
    }

    /**
     * Supprime l'évaluation
     */
    public static function deleteEvaluation($idUser, $idEntreprise)
    {
        $db = Database::getInstance();
        $req = $db->prepare("DELETE FROM Evaluer WHERE Id_Utilisateur = ? AND Id_ENTREPRISE = ?");
        return $req->execute([$idUser, $idEntreprise]);
    }

    /**
     * Récupère toutes les évaluations laissées par un utilisateur (Pilote ou Admin)
     */
    public static function getEvaluationsByUser($idUser)
    {
        $db = Database::getInstance();
        $sql = "SELECT Evaluer.*, ENTREPRISE.nom AS nom_entreprise 
                FROM Evaluer 
                JOIN ENTREPRISE ON Evaluer.Id_ENTREPRISE = ENTREPRISE.Id_ENTREPRISE 
                WHERE Evaluer.Id_Utilisateur = ? 
                ORDER BY Evaluer.note DESC"; // On trie pour afficher les meilleures notes en premier
                
        $req = $db->prepare($sql);
        $req->execute([$idUser]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère la liste de toutes les évaluations pour une entreprise donnée
     * (avec le prénom et le nom du pilote qui a laissé la note)
     */
    public static function getEvaluationsByEntreprise($idEntreprise)
    {
        $db = Database::getInstance();
        $sql = "SELECT Evaluer.note, Evaluer.avis, Utilisateur.prenom, Utilisateur.nom 
                FROM Evaluer 
                JOIN Utilisateur ON Evaluer.Id_Utilisateur = Utilisateur.Id_Utilisateur 
                WHERE Evaluer.Id_ENTREPRISE = ? 
                ORDER BY Evaluer.note DESC";
                
        $req = $db->prepare($sql);
        $req->execute([$idEntreprise]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}