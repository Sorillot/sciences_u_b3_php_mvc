<?php

namespace App\Controller;

use App\Payement;

class PayementController extends AbstractController
{

  //affiche l'interface de paiement
  public function index()
  {

    echo $this->twig->render('Payement.html.twig');
  }
  
  //permet de créer un nouvel utilisateur
  public function CreateNewUser()
  {
    $this->payement->CreateUtilisateurFromCreditCardAndReturnUser('4242424242424242',6,2022,'314','test','test@gmail.com');
  }
  
  //faire un nouveau payement
  public function Pay()
  {
    $facture = $this->payement->CreateUserAndPay('4242424242424242',6,2022,'314','pedro','pedro@gmail.com',50000,'eur','payement');
    echo $this->twig->render('paid.html.twig', ['facture' => $facture]);
  }
  
  //créer un nouvel abonnement
  public function CreateAbonnement()
  {
    $this->payement->createSubscription('Prenium',500,'month','eur');
  }
  
  //abonne un utilisateur
  public function AbonnerUnUtilisateurAUnAbonnement($idPlan,$idUser)
  {
    $this->payementt->SubscribetoPlan($idPlan,$idUser);
  }
}
