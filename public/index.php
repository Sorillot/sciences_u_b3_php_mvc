<?php

// Composer va installer la méthode de chargement PSR-4 auprès de PHP
require_once __DIR__ . '/../vendor/autoload.php';
require_once('/wamp64/www/SUPHP/MVC/vendor/stripe/stripe-php/init.php');

if (php_sapi_name() !== 'cli' && preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])) {
  // On demande à PHP de servir le fichier demandé directement
  return false;
}

use App\Controller\HomeController;
use App\Router;
use App\Payement;
use App\StripePayment;
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

$router = new Router($entityManager, $twig);
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


// $router->execute($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);

$payement = new Payement();

//Création d'utilisateur + payement
$user = $payement->CreateUserAndPay('4242424242424242',6,2022,'314','pedro','pedro@gmail.com',50000,'eur','payement');

//Création d'utilisateur sans payement
$payement->CreateUtilisateurFromCreditCardAndReturnUser('4242424242424242',6,2022,'314','test','test@gmail.com');

//Création d'abonnement
$newSubscription = $payement->createSubscription('SubscriptionTest',500,'month','eur');
var_dump($newSubscription);
$payement->SubscribetoPlan($newSubscription->id,$user->id);


