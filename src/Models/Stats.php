<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Stats
{
    /**
     * Récupère les 4 indicateurs clés pour le carrousel de l'accueil
     */
    public static function getDashboardStats()
    {
        $db = Database::getInstance();
        
        // 1. Nombre total d'offres disponibles en base
        $reqTotal = $db->query("SELECT COUNT(*) as total FROM OFFRE");
        $totalOffres = $reqTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        
        // 2. Répartition par type de contrat (Stage vs Alternance)
        $reqRepart = $db->query("SELECT type_contrat, COUNT(*) as total FROM OFFRE GROUP BY type_contrat");
        $repartition = $reqRepart->fetchAll(PDO::FETCH_ASSOC);
        
        $repartitionText = [];
        foreach($repartition as $row) {
            $repartitionText[] = $row['total'] . ' ' . $row['type_contrat'] . 's';
        }
        $repartitionString = implode(' | ', $repartitionText);
        if(empty($repartitionString)) $repartitionString = "Données insuffisantes";

        // 3. Top des offres les plus ajoutées en wish-list
        $reqTop = $db->query("SELECT O.titre, E.nom AS entreprise, COUNT(F.Id_OFFRE) AS nb 
                              FROM Mettre_Favori F 
                              JOIN OFFRE O ON F.Id_OFFRE = O.Id_OFFRE 
                              JOIN ENTREPRISE E ON O.Id_ENTREPRISE = E.Id_ENTREPRISE 
                              GROUP BY F.Id_OFFRE 
                              ORDER BY nb DESC LIMIT 1");
        $topWishlist = $reqTop->fetch(PDO::FETCH_ASSOC);
        $topWishlistText = $topWishlist ? $topWishlist['titre'] . ' (' . $topWishlist['entreprise'] . ')' : 'Aucun favori pour le moment';

        // 4. Nombre moyen de candidatures par offre
        // NOTE : Assure-toi que ta table s'appelle bien "Postuler". Si c'est "Candidater", change le nom ci-dessous.
        $moyenneCandidatures = "N/A";
        try {
            $reqCand = $db->query("SELECT COUNT(*) as total_cand FROM Postuler");
            $totalCand = $reqCand->fetch(PDO::FETCH_ASSOC)['total_cand'] ?? 0;
            if ($totalOffres > 0) {
                $moyenneCandidatures = round($totalCand / $totalOffres, 1);
            } else {
                $moyenneCandidatures = 0;
            }
        } catch (\Exception $e) {
            // Si la table n'existe pas encore ou porte un autre nom
            $moyenneCandidatures = "?";
        }

        // On renvoie un tableau propre avec toutes les stats
        return [
            'total_offres' => $totalOffres,
            'repartition' => $repartitionString,
            'top_wishlist' => $topWishlistText,
            'moyenne_candidatures' => $moyenneCandidatures
        ];
    }
}