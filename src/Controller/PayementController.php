<?php

namespace App\Controller;

use App\Payement;

class PayementController extends AbstractController
{
  // Ici, j'ai une dépendance envers l'entity manager
  public function index()
  {

    echo $this->twig->render('newPayement.html.twig');
  }

  public function CreateNewUser()
  {
    $this->payement->CreateUtilisateurFromCreditCardAndReturnUser('4242424242424242',6,2022,'314','test','test@gmail.com');
  }

  public function Pay()
  {
    $this->payement->CreateUserAndPay('4242424242424242',6,2022,'314','pedro','pedro@gmail.com',50000,'eur','payement');
  }

  public function CreateAbonnement()
  {
    $this->payement->createSubscription('Prenium',500,'month','eur');
  }

  public function AbonnerUnUtilisateurAUnAbonnement($idPlan,$idUser)
  {
    $this->payementt->SubscribetoPlan($idPlan,$idUser);
  }
}
