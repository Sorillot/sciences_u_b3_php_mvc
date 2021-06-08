<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;

class HomeController
{
  // Ici, j'ai une dépendance envers l'entity manager
  public function index(EntityManager $em)
  {
    $user = new User();
    $user->setName("Bob");

    // Persist permet uniquement de dire au gestionnaire d'entités de gérer l'entité passée en paramètre
    // Persist ne déclenche pas automatiquement une insertion
    $em->persist($user);
    // Pour déclencher l'insertion, on doit appeler la méthode "flush" sur le gestionnaire d'entités
    $em->flush();

    var_dump($user);
  }

  public function contact()
  {
    echo "Contact";
  }
}
