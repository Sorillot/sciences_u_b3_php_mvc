# Sciences-U - B3 IW - PHP7 MVC from scratch -> Ajout d'un moyen de payement (Stripe)

- [Introduction ](#Introduction)
- [Création du module de Payement ](#Payement)
- [Fonctionnalité ](#Fonctionnalité)


## Introduction

Dans un premier temps nous devons installer le module stripe via composer 

`composer require stripe/stripe-php`

Nous allons ici travailler avec l'interface web de stripe, il faudra donc efféctuer 2 choses : 
  -> Se créer un compte Stripe  : https://dashboard.stripe.com/
  -> récuperer la clé secrete d'api a l'adresse suivante : https://dashboard.stripe.com/test/apikeys
  
 
  
## Payement
A la racine de src j'ai créer une page Payement.php avec un ;
 
 `namespace App;`
 
 j'ai ajouté les différents use : 
 
  ```
  use Stripe;
  use Stripe\Plan;
  use Stripe\Stripe as StripeStripe;
  ```
  
  sans oublier le require  du module de stripe
  
  `require_once('/wamp64/www/SUPHP/MVC/vendor/stripe/stripe-php/init.php');`
 
## Fonctionnalité

Présentation des différentes fonctionnalité installée : 

La création d'un utilisateur 

'''
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
'''

Dans la création d'un utilisateur on peut observer la présence d'un token, ce token est créer via la carte bancaire : 

'''
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
'''

Dans le cadre de notre Api Stripe il est conseillé de mettre le numéro de carte bancaire : 4242424242424242 ainsi que le cvc : 314.

On peut aussi créer une charge ( un payement ) grace au code suivant : 

'''
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
'''

J'ai aussi ajouté le fait de pouvoir créer un abonnement sur un utilisateur, dans le cadre de Stripe un abonnement ce fait au travers d'un produits. Il faut donc créer un produit puis lié un abonnement au produits afin de lié un utilisateur a cet abonnement.

La création de produit est assez simpliste : 
'''
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
'''
Une fois le produit créer on peut créer un plan ( abonnement ) qui est lié a l'id de ce produit : 
'''
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
    '''
   
   Et grace a l'id de cet abonnement et l'id de l'utilisateur on peut ajouter un abonnement a l'utilisateur.
   '''
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
'''

J'ai créé quelque méthodes pour simplifier le travail : 

Une méthode qui permet de directement créer un utilisateur :
'''
    ///Permet de créer un nouvel utilisateur depuis sa carte de crédit
    public function CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email){
        $token = Payement::CreateToken($cardNumber,$expMonth,$expYear,$cvc);
        $user = Payement::CreatCustomer($token,$name,$email);
        return $user;
    }
'''
Une méthode qui permet de directement créer un utilisateur ainsi que de le faire payer une somme indiquée:
'''
    //Permet de créer un utilisateur et de payer directement
    public function CreateUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description){
        $user = Payement::CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
        Payement::CreateCharge($amount,$currency,$description,$user->id);
        return $user;
    }
'''

Une méthode qui permet de rapidement créer un abonnement : 
'''
    //Créer un produit et lui associe un prix d'abonnement puis retourne l'abonnement
    public function createSubscription($NameoftheSubscription,$amount,$interval,$currency){
        $product = Payement::CreateProduct($NameoftheSubscription);
        $Plan = Payement::CreatePlan($amount,$interval,$currency,$NameoftheSubscription,$product->id);

        return $Plan;
    }
  '''
