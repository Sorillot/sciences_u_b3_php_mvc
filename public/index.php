<?php

//composer installe la méthode de chargement PSR4
require_once __DIR__.'/../vendor/autoload.php';

//appel des classes avec "use" en indiquant leur FQCN
use App\Entity\User;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

//Dossier ou récuperer les entités pour doctrine
$path = [__DIR__.'/../src/Entity'];
$isDevMode = true;

$dbParams = [
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'host' => 'localhost',
    'password' => '',
    'dbname' => 'php_mvc'
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