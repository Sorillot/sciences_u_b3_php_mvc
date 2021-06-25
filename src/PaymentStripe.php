<?php

namespace App;
use Stripe;
use Stripe\Plan;
use App\PaymentModule;
use Stripe\Stripe as StripeStripe;

require_once __DIR__ . '/../vendor/stripe/stripe-php/init.php';

const SK_API = 'sk_test_51J4TisIYmMa000Y7CNWlQkBAfyuYmNBT1TsC6MsxWC6WBQVKs219ZhAwMb0H6nFF4NWPYYOfKO8Gf3a6YzpJLgMv00Dbs3HYv8';

class PaymentStripe extends PaymentModule {

    public function __construct()
    {
        
    }

    ///Permet de créer un nouvel utilisateur depuis sa carte de crédit
    public function createUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email){
        $token = PaymentStripe::CreateToken($cardNumber,$expMonth,$expYear,$cvc);
        $user = PaymentStripe::CreatCustomer($token,$name,$email);
        return $user;
    }

    //Permet de créer un utilisateur et de payer directement
    public function createUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description){
        $user = PaymentStripe::CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
        PaymentStripe::CreateCharge($amount,$currency,$description,$user->id);
        return $user;
    }

    //Créer un produit et lui associe un prix d'abonnement puis retourne l'abonnement
    public function createSubscription($NameoftheSubscription,$amount,$interval,$currency){
        $product = PaymentStripe::CreateProduct($NameoftheSubscription);
        $Plan = PaymentStripe::CreatePlan($amount,$interval,$currency,$NameoftheSubscription,$product->id);

        return $Plan;
    }

    //Créer un token qui sera par la suite l'id de l'utilisateur
    public function createToken($cardNumber,$expMonth,$expYear,$cvc){
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
    public function creatCustomer($token,$name,$email){
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
    public function createCharge($amount,$currency,$description,$token){
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
    public function createProduct($name){
        $stripe = new \Stripe\StripeClient(
            SK_API
        );
        $product = $stripe->products->create([
            'name' => $name,
        ]);

        return $product;
    }

    //liste tous les produits existants
    public function listAllProducts(){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          return $stripe->products->all(['limit' => 3]);
    }

    //permet de creér un abonnement a un produits
    public function createPlan($amount,$interval,$currency,$name,$idPlan){
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
    public function subscribetoPlan($idPlan,$token){
        $stripe = new \Stripe\StripeClient(
            SK_API
          );
          $stripe->subscriptions->create([
            'customer' => $token,
            'plan' => $idPlan
          ]);
    }


}
