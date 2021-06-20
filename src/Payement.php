<?php

namespace App;
use Stripe;
use Stripe\Plan;
use Stripe\Stripe as StripeStripe;

require_once('/wamp64/www/SUPHP/MVC/vendor/stripe/stripe-php/init.php');

const SK_API = 'sk_test_51J4TisIYmMa000Y7CNWlQkBAfyuYmNBT1TsC6MsxWC6WBQVKs219ZhAwMb0H6nFF4NWPYYOfKO8Gf3a6YzpJLgMv00Dbs3HYv8';

class Payement{

    public function __construct()
    {
        
    }

    ///Permet de créer un nouvel utilisateur depuis sa carte de crédit
    public function CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email){
        $token = Payement::CreateToken($cardNumber,$expMonth,$expYear,$cvc);
        $user = Payement::CreatCustomer($token,$name,$email);
        return $user;
    }

    //Permet de créer un utilisateur et de payer directement
    public function CreateUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description){
        $user = Payement::CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
        Payement::CreateCharge($amount,$currency,$description,$user->id);
        return $user;
    }

    //Créer un produit et lui associe un prix d'abonnement puis retourne l'abonnement
    public function createSubscription($NameoftheSubscription,$amount,$interval,$currency){
        $product = Payement::CreateProduct($NameoftheSubscription);
        $Plan = Payement::CreatePlan($amount,$interval,$currency,$NameoftheSubscription,$product->id);

        return $Plan;
    }

    //Créer un token qui sera par la suite l'id de l'utilisateur
    public function CreateToken($cardNumber,$expMonth,$expYear,$cvc){
        $stripe = new \Stripe\StripeClient(SK_API);
          $token = $stripe->tokens->create([
            'card' => [
              'number' => $cardNumber,
              'exp_month' => $expMonth,
              'exp_year' => $expYear,
              'cvc' => $cvc
            ],
          ]);

          return $token;
    }


    //Créer un utilisateur (Le token doit etre récuperer grace a CreateToken)
    public function CreatCustomer($token,$name,$email){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          $customer = $stripe->customers->create([
            'source' => $token,
            'description' => $name,
            'email' => $email
          ]);

          return $customer;
    }


    // Permet de faire payer une somme a un utilisateur
    public function CreateCharge($amount,$currency,$description,$token){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          $stripe->charges->create([
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'customer' =>  $token
          ]);
    }

    //Permet de créer un produits qui sera par la suite utilisé par exemple pour un abonnement
    public function CreateProduct($name){
        $stripe = new \Stripe\StripeClient(
            SK_API
        );
        $product = $stripe->products->create([
            'name' => $name,
        ]);

        return $product;
    }

    //liste tous les produits existants
    public function ListAllProducts(){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          return $stripe->products->all(['limit' => 3]);
    }

    //permet de creér un abonnement a un produits
    public function CreatePlan($amount,$interval,$currency,$name,$idPlan){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          return $stripe->plans->create([
            'amount' => $amount,
            'interval' => $interval,
            'currency' => $currency,
            'product' => $idPlan,
            'id' =>  $idPlan
          ]);
    }

    //permet de lié un abonnement et un utilisateur
    public function SubscribetoPlan($idPlan,$token){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          $stripe->subscriptions->create([
            'customer' => $token,
            'plan' => $idPlan
          ]);
    }


}
