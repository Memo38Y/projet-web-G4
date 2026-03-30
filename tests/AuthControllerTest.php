<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\AuthController;
use Twig\Environment;

class AuthControllerTest extends TestCase
{
    public function testLoginAffichePageSansErreurEnGet()
    {
        // 1. On crée un "Mock" (un faux objet) de Twig pour simuler son comportement
        $twigMock = $this->createMock(Environment::class);

        // 2. On définit les attentes : 
        // Le contrôleur DOIT appeler la méthode 'render' de Twig avec les bons paramètres
        $twigMock->expects($this->once())
                 ->method('render')
                 ->with('connexion.html.twig', ['erreur' => null])
                 ->willReturn('<h1>Page de connexion mockée</h1>');

        // 3. On simule une requête HTTP de type GET (l'utilisateur arrive sur la page)
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_POST = []; // On s'assure qu'aucune donnée de formulaire n'est envoyée

        // 4. On instancie le contrôleur en lui passant notre faux moteur Twig
        $controller = new AuthController($twigMock);

        // 5. Comme ta méthode login() fait un "echo", on intercepte l'affichage
        ob_start();
        $controller->login();
        $output = ob_get_clean();

        // 6. L'assertion : on vérifie que le contrôleur a bien affiché ce que Twig a généré
        $this->assertEquals('<h1>Page de connexion mockée</h1>', $output);
    }
}