<?php
// public/index.php
session_start();

// 1. On charge tous les outils via Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

// 2. On prépare Twig pour qu'il sache où trouver les fichiers HTML
$loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/views');
$twig = new \Twig\Environment($loader, [
    'cache' => false, // On désactive le cache pendant qu'on code
    'debug' => true,   // On active le mode debug pour Twig
]);
$twig->addGlobal('session', $_SESSION);

// 3. On récupère l'URL demandée (sans les paramètres ?id=...)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 4. LE ROUTEUR : On aiguille l'utilisateur selon l'URL
switch ($uri) {
    case '/':
        // On instancie le contrôleur de l'accueil et on lance la méthode index()
        $homeController = new \App\Controllers\HomeController($twig);
        $homeController->index();
        break;

    case '/entreprises':
        $entrepriseController = new \App\Controllers\EntrepriseController($twig);
        $entrepriseController->index();
        break;

    case '/connexion':
        $authController = new \App\Controllers\AuthController($twig);
        $authController->login();
        break;
    
    case '/profil':
        $profilController = new \App\Controllers\ProfilController($twig);
        $profilController->index();
        break;
        
    case '/deconnexion':
        $authController = new \App\Controllers\AuthController($twig);
        $authController->logout();
        break;

    case '/api/favori/toggle':
        $favoriController = new \App\Controllers\FavoriController();
        $favoriController->toggleApi();
        break;

    case '/entreprise_offres':
        $entrepriseController = new \App\Controllers\EntrepriseController($twig);
        $entrepriseController->showOffres();
        break;

    case '/offre':
        $offreController = new \App\Controllers\OffreController($twig);
        $offreController->show();
        break;

    case '/admin/etudiants':
        $adminController = new \App\Controllers\AdminController($twig);
        $adminController->gererEtudiants();
        break;

    default:
        // Si l'URL n'existe pas, on renvoie une 404
        http_response_code(404);
        echo "Erreur 404 : Cette page n'existe pas !";
        break;
}