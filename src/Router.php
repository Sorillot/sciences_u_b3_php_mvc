<?php

namespace App;

use Doctrine\ORM\EntityManager;
use ReflectionMethod;
use Twig\Environment;

class Router
{
  private $paths = [];
  // Paramètres injectables dans les méthodes de contrôleurs
  private $params = [];
  private $twigInstance;

  public function __construct(EntityManager $em, Environment $twig)
  {
    $this->params[EntityManager::class] = $em;
    $this->twigInstance = $twig;
  }

  public function addPath(string $path, string $httpMethod, string $name, string $class, string $method)
  {
    $this->paths[] = ([
      'path' => $path,
      'http_method' => $httpMethod,
      'name' => $name,
      'class' => $class,
      'method' => $method
    ]);
  }

  public function execute(string $requestPath, string $requestMethod)
  {
    if ($path = $this->checkPath($requestPath, $requestMethod)) {
      // Récupération nom de la classe et nom de la méthode
      $className = $path['class'];
      $methodName = $path['method'];
      // Initialisation des paramètres qui vont être injectés
      // Par défaut : aucun, donc tableau vide
      $params = [];

      // Récupération des infos de la méthode avec Reflection
      $methodInfos = new ReflectionMethod($className . '::' . $methodName);
      // Récupération des paramètres de la méthode
      $parameters = $methodInfos->getParameters();

      // Analyse des différents paramètres
      // Du coup, pas de boucle si pas de paramètre
      foreach ($parameters as $param) {
        $paramName = $param->getName();
        $paramType = $param->getType();
        $typeName = $paramType->getName();
        // Vérification si le nom du paramètre existe dans les paramètres injectables
        if (array_key_exists($typeName, $this->params)) {
          // Enregistrement du paramètre dans les paramètres à injecter
          $params[$paramName] = $this->params[$typeName];
        }
      }

      // Instanciation du contrôleur
      $controller = new $className($this->twigInstance);

      // Appel de la méthode adéquate, avec le(s) paramètre(s) adéquat(s), ou aucun paramètre
      call_user_func_array(
        [$controller, $methodName],
        $params
      );
    } else {
      http_response_code(404);
    }
  }

  public function checkPath(string $requestPath, string $requestMethod)
  {
    foreach ($this->paths as $path) {
      if ($path['path'] === $requestPath && $path['http_method'] === $requestMethod) {
        return $path;
      }
    }

    return false;
  }
}
