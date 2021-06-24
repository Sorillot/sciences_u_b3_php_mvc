<?php

// Composer va installer la méthode de chargement PSR-4 auprès de PHP
require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() !== 'cli' && preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])) {
  // On demande à PHP de servir le fichier demandé directement
  return false;
}

use App\Controller\HomeController;
use App\Controller\PayementController;
use App\Payement;
use App\Router;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Configuration, variables d'environnement
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// Doctrine
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


$test = $_ENV['STRIPE_PRIVATE_KEY'];

$config = Setup::createAnnotationMetadataConfiguration(
  $paths,
  $isDevMode,
  null,
  null,
  false
);

$entityManager = EntityManager::create($dbParams, $config);

// Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
  'debug' => ($_ENV['APP_ENV'] === 'dev'),
  'cache' => __DIR__ . '/../var/twig',
]);

$payement = new Payement( $_ENV['STRIPE_PRIVATE_KEY']);

$router = new Router($entityManager, $twig, $payement);

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
$router->addPath(
  '/payement',
  'GET',
  'payement',
  PayementController::class,
  'index'
);
$router->addPath(
  '/pay',
  'GET',
  'payement',
  PayementController::class,
  'Pay'
);

$router->execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);



// 

// //Création d'utilisateur + payement
// $user = $payement->CreateUserAndPay();

// //Création d'utilisateur sans payement
// $payement->CreateUtilisateurFromCreditCardAndReturnUser('4242424242424242',6,2022,'314','test','test@gmail.com');

// //Création d'abonnement
// 

// //abonner un utilisateur a un abonnement
// $payement->SubscribetoPlan($newSubscription->id,$user->id);


