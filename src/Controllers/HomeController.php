<?php
namespace App\Controllers;

class HomeController
{
    private $twig;

    public function __construct($twig)
    {
        $this->twig = $twig;
    }

    public function index()
    {
        $offresTest = [
            ['titre' => 'Développeur Web Laravel', 'lieu' => 'Paris'],
            ['titre' => 'Designer UX/UI', 'lieu' => 'Lyon'],
            ['titre' => 'Assistant Chef de Projet', 'lieu' => 'Bordeaux']
        ];
        
        echo $this->twig->render('accueil.html.twig', [
            'dernieresOffres' => $offresTest
        ]);
    }
}