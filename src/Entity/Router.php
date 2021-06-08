<?php

namespace App;

use Doctrine\ORM\EntityManager;
use ReflectionMethod;

class Router
{
    private $paths = [];
    private $params = [];

    public function __construct(EntityManager $em)
    {
        $this->params['em'] = $em;
    }

    public function addPath(string $path, string $httpMethod, string $name, string $class, string $method)
    {
        $this->paths = ([
            [
            'path' => $path,
            'method' => $httpMethod,
            'name' => $name,
            'class' => $class,
            'method' => $method,
            ]
        ]);
    }

    public function execute(string $requestPath, string $requestMethod)
    {
        if ($path =  $this->checkPath($requestPath, $requestMethod)){
            //Récupération du nom de la classe et de la méthode

            $params = [];

            $className = $path['class'];
            $methodName = $path['method'];

            $methodInfo = new ReflectionMethod($className . '::' . $methodName);
            $parameters = $methodInfo->getParameters();

            foreach($parameters as $param){
                $paramName = $param->getName();
                $paramType = $param->getType();
                $typeName = $paramType->getName();
                if(array_key_exists($typeName,$this->params)){
                    $params[$paramName] = $this->params[$typeName];
                }
            }

            $controller = new $className;

            call_user_func_array(
                [$controller,$methodName],
            $params
            );

        } else {
        //return
        http_response_code(404);
        }
    }

    public function checkPath($requestPath, string $requestMethod)
    {
        foreach($this->paths as $path){
            if($path['path'] === $path && $path['method'] === $requestMethod){
                return $path;
            }
        }
        return false;
    }

    // public function getPath(){
    //     return $this->paths;
    // }
    
    // public function setPaths($paths){
    //     $this->paths = $paths;

    //     return $this;
    // }
    
    public function registerRoutes(){

    }
}