<?php
namespace App\Controllers;

use App\Models\Favori;

class FavoriController
{
    public function toggleApi()
    {
        // On s'assure que c'est bien un étudiant connecté
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }

        $idOffre = $_POST['id_offre'] ?? null;
        if ($idOffre) {
            // On appelle notre modèle
            $status = Favori::toggle($_SESSION['user']['id'], $idOffre);
            
            // On renvoie la réponse au JavaScript
            echo json_encode(['status' => $status]);
        }
        exit;
    }
}