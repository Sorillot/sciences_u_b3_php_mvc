<?php

//composer installe la méthode de chargement PSR4
require_once __DIR__.'/../vendor/autoload.php';

//appel des classes avec "use" en indiquant leur FQCN
use App\Entity\User;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->LoadEnv(__DIR__.'/../.env');

//Dossier ou récuperer les entités pour doctrine
$path = [__DIR__.'/../src/Entity'];
$isDevMode = true;

$dbParams = [
    'driver' => $_ENV['DB_DRIVER'],
    'user' => $_ENV['DB_USER'],
    'host' => $_ENV['DB_HOST'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname' => $_ENV['DB_DBNAME'],
];

$config = Setup::createAnnotationMetadataConfiguration(
    $path,
    $isDevMode,
    null,
    null,
    false
);

$entityManager = EntityManager::create($dbParams,$config);

$user = new User();
$user->setName('Bob');
$entityManager->persist($user);
$entityManager->flush();

?>