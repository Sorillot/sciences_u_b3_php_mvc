<?php

// Composer va installer la méthode de chargement PSR-4 auprès de PHP
require_once __DIR__ . '/../vendor/autoload.php';

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
