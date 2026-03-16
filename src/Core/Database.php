<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    // Les informations de ta base MariaDB
    private const DB_HOST = '127.0.0.1';
    private const DB_NAME = 'web4all_db';
    private const DB_USER = 'web4all_root';       
    private const DB_PASS = 'W4Aroot';           

    // Empêche l'instanciation classique
    private function __construct() {}

    // Empêche le clonage
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=utf8mb4";
                
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}