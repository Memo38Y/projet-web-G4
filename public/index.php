<?php
// public/index.php

// 1. On charge tous les outils via Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. On prépare Twig pour qu'il sache où trouver les fichiers HTML
$loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // On désactive le cache pendant qu'on code
]);

// 3. On récupère l'URL demandée (sans les paramètres ?id=...)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 4. LE ROUTEUR : On aiguille l'utilisateur selon l'URL
switch ($uri) {
    case '/postuler':
        // Plus tard, on appellera un PostulerController ici
        echo $twig->render('layout.html.twig', ['titre' => 'Postuler à une offre']);
        break;
    case '/':
        // On instancie le contrôleur de l'accueil et on lance la méthode index()
        $homeController = new \App\Controllers\HomeController($twig);
        $homeController->index();
        break;

    case '/entreprises':
        // Page de test pour voir si le routeur marche
        echo $twig->render('entreprise.html.twig', ['titre' => 'Entreprises', 
            'entreprises' => 'Google, Facebook, Amazon, Microsoft, Apple']);
        break;

    default:
        // Si l'URL n'existe pas, on renvoie une 404
        http_response_code(404);
        echo "Erreur 404 : Cette page n'existe pas !";
        break;
}