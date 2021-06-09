<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Twig\Environment;

class HomeController
{
  // Ici, j'ai une dépendance envers l'entity manager
  public function index(EntityManager $em, Environment $twig)
  {
    $user = new User();
    $user->setName("Bob");

    // Persist permet uniquement de dire au gestionnaire d'entités de gérer l'entité passée en paramètre
    // Persist ne déclenche pas automatiquement une insertion
    $em->persist($user);
    // Pour déclencher l'insertion, on doit appeler la méthode "flush" sur le gestionnaire d'entités
    $em->flush();

    echo $twig->render('index.html.twig', ['user' => $user]);
  }

  public function contact(Environment $twig)
  {
    echo $twig->render('contact.html.twig', ['title' => 'Contact']);
  }
}
