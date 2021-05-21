<?php

// Composer va installer la méthode de chargement PSR-4 auprès de PHP
require_once __DIR__ . '/../vendor/autoload.php';

if (php_sapi_name() !== 'cli' && preg_match('/\.(?:png|jpg|jpeg|gif|ico)$/', $_SERVER['REQUEST_URI'])) {
  // On demande à PHP de servir le fichier demandé directement
  return false;
}

use App\Entity\User;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

// Indique à Doctrine dans quel dossier aller chercher & analyser les entités
$paths = [__DIR__ . '/../src/Entity'];
$isDevMode = true;

$dbParams = [
  'driver'   => 'pdo_mysql',
  'host'     => 'localhost',
  'port'     => '3640',
  'user'     => 'root',
  'password' => 'mysqltests',
  'dbname'   => 'php_mvc'
];

$config = Setup::createAnnotationMetadataConfiguration(
  $paths,
  $isDevMode,
  null,
  null,
  false
);

$entityManager = EntityManager::create($dbParams, $config);

$user = new User();
$user->setName("Bob");

// Persist permet uniquement de dire au gestionnaire d'entités de gérer l'entité passée en paramètre
// Persist ne déclenche pas automatiquement une insertion
$entityManager->persist($user);
// Pour déclencher l'insertion, on doit appeler la méthode "flush" sur le gestionnaire d'entités
$entityManager->flush();

var_dump($user);
