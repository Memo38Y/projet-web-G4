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
}