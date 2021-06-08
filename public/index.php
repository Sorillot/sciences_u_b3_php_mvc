<?php

// Composer va installer la méthode de chargement PSR-4 auprès de PHP
require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() !== 'cli' && preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])) {
  // On demande à PHP de servir le fichier demandé directement
  return false;
}

use App\Controller\HomeController;
use App\Entity\User;
use App\Router;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// Indique à Doctrine dans quel dossier aller chercher & analyser les entités
$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = ($_ENV['APP_ENV'] === 'dev');

$dbParams = [
  'driver'   => $_ENV['DB_DRIVER'],
  'host'     => $_ENV['DB_HOST'],
  'port'     => $_ENV['DB_PORT'],
  'user'     => $_ENV['DB_USER'],
  'password' => $_ENV['DB_PASSWORD'],
  'dbname'   => $_ENV['DB_DBNAME']
];

$config = Setup::createAnnotationMetadataConfiguration(
  $paths,
  $isDevMode,
  null,
  null,
  false
);

$entityManager = EntityManager::create($dbParams, $config);

$router = new Router($entityManager);
$router->addPath(
  '/',
  'GET',
  'home',
  HomeController::class,
  'index'
);
$router->addPath(
  '/contact',
  'GET',
  'contact',
  HomeController::class,
  'contact'
);

$router->execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
