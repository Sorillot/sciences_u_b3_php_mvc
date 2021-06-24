# Sciences-U - B3 IW - PHP7 MVC from scratch -> Ajout d'un moyen de payement (Stripe)

- [Introduction ](#Introduction)
- [Création du module de Payement ](#Payement)
- [Fonctionnalité ](#Fonctionnalité)
- [Modification ](#Modification)

## Introduction

Dans un premier temps nous devons installer le module stripe via composer 

`composer require stripe/stripe-php`

Nous allons ici travailler avec l'interface web de stripe, il faudra donc efféctuer 2 choses : 
  -> Se créer un compte Stripe  : https://dashboard.stripe.com/
  -> récuperer la clé secrete d'api a l'adresse suivante : https://dashboard.stripe.com/test/apikeys
  
Chaque ajout / Fonctionnalité pourra donc etre vérifier en temps réel via  https://dashboard.stripe.com/
afin de bien recevoir les données il faudra modifier la constante SK_API renseigner dans la page Payement.php et la remplacer par la votre :


    require_once('/wamp64/www/SUPHP/MVC/vendor/stripe/stripe-php/init.php');

    const SK_API = 'sk_test_51J4TisIYmMa000Y7CNWlQkBAfyuYmNBT1TsC6MsxWC6WBQVKs219ZhAwMb0H6nFF4NWPYYOfKO8Gf3a6YzpJLgMv00Dbs3HYv8';

    class Payement{
  
 
  
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
    


Dans la création d'un utilisateur on peut observer la présence d'un token, ce token est créer via la carte bancaire : 



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
    


Dans le cadre de notre Api Stripe il est conseillé de mettre le numéro de carte bancaire : 4242424242424242 ainsi que le cvc : 314.

On peut aussi créer une charge ( un payement ) grace au code suivant : 



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
    


J'ai aussi ajouté le fait de pouvoir créer un abonnement sur un utilisateur, dans le cadre de Stripe un abonnement ce fait au travers d'un produits. Il faut donc créer un produit puis lié un abonnement au produits afin de lié un utilisateur a cet abonnement.

La création de produit est assez simpliste :



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
    


Une fois le produit créer on peut créer un plan ( abonnement ) qui est lié a l'id de ce produit : 


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
    
   
   Et grace a l'id de cet abonnement et l'id de l'utilisateur on peut ajouter un abonnement a l'utilisateur.
   
  
   
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
    


J'ai créé quelque méthodes pour simplifier le travail : 

Une méthode qui permet de directement créer un utilisateur :



    ///Permet de créer un nouvel utilisateur depuis sa carte de crédit
    public function CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email){
        $token = Payement::CreateToken($cardNumber,$expMonth,$expYear,$cvc);
        $user = Payement::CreatCustomer($token,$name,$email);
        return $user;
    }



Une méthode qui permet de directement créer un utilisateur ainsi que de le faire payer une somme indiquée:



    //Permet de créer un utilisateur et de payer directement
    public function CreateUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description){
        $user = Payement::CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
        Payement::CreateCharge($amount,$currency,$description,$user->id);
        return $user;
    }
    


Une méthode qui permet de rapidement créer un abonnement : 



    //Créer un produit et lui associe un prix d'abonnement puis retourne l'abonnement
    public function createSubscription($NameoftheSubscription,$amount,$interval,$currency){
        $product = Payement::CreateProduct($NameoftheSubscription);
        $Plan = Payement::CreatePlan($amount,$interval,$currency,$NameoftheSubscription,$product->id);

        return $Plan;
    }
    
    
## Modification 

Dans un premier temps j'ai repris les Issues

1 - Constante SK_APi

Ajout de la constante dans le fichier .env et .env.local

    //.env
    STRIPE_PRIVATE_KEY=xxxxxxx

    //.env.local
    STRIPE_PRIVATE_KEY=sk_test_51J4TisIYmMa000Y7CNWlQkBAfyuYmNBT1TsC6MsxWC6WBQVKs219ZhAwMb0H6nFF4NWPYYOfKO8Gf3a6YzpJLgMv00Dbs3HYv8
    
    
 2 - Require Once inutile

Les 2 requires once était bien inutile et je les ais simplement supprimé.


3 - Methode de classe vs méthodes statique

      
    ///Permet de créer un nouvel utilisateur depuis sa carte de crédit
    public function CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email){
        $token = $this->CreateToken($cardNumber,$expMonth,$expYear,$cvc);
        $user = $this->CreatCustomer($token,$name,$email);
        return $user;
    }

    //Permet de créer un utilisateur et de payer directement
    public function CreateUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description){
        $user = $this->CreateUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
        $this->CreateCharge($amount,$currency,$description,$user->id);
        return $user;
    }

    //Créer un produit et lui associe un prix d'abonnement puis retourne l'abonnement
    public function createSubscription($NameoftheSubscription,$amount,$interval,$currency){
        $product = $this->CreateProduct($NameoftheSubscription);
        $Plan = $this->CreatePlan($amount,$interval,$currency,$NameoftheSubscription,$product->id);

        return $Plan;
    }
J'ai changer les appels des méthodes pour ne pas daire d'appel de méthode statique

4 - Exemple de paiement

Pour développer un peu plus mon systeme j'ai ajouter un controller : PayementController avec différentes fonctions.
    

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
    
 De manière a pouvoir faire appel au module de paiement dans n'importe quel controller j'ai ajouté le paiement directement dans la Abstract Controller : 
 
 
    abstract class AbstractController
    {
      protected $twig;
      protected $payement;

      public function __construct(Environment $twig, Payement $payement)
      {
        $this->twig = $twig;
        $this->payement = $payement;
      }
    }
    
 Puis dans l'instanciation du controller dans le Routeur.php
 
       // Instanciation du contrôleur
      $controller = new $className($this->twigInstance, $this->payementInstance);
    
 J'ai créer un interface de paiement basique appelé Payement.html.twig (Pas terminé actuellement)
 
      {% extends 'base.html.twig' %}

    {% block body %}
    <form action="">
      <label>Nouveau paiement</label>


      <label>Numéro</label>
      <input type="text">
      <label>Mois</label>
      <input type="number">
      <label>Année</label>
      <input type="number">
      <label>CVC</label>
      <input type="number">

    </form>
    {% endblock %}
      
  Et un interface correspondant a un paiement effectué : paid.html.twig
  
      {% extends 'base.html.twig' %}

      {% block body %}
        <h1>Le paiement de </h1>
        <p>
          {{ (facture.email) }}
          a bien été enregistré.
        </p>
      {% endblock %}

      
 J'ai donc ensuite ajouté dans l'index la route pour accéder a mon paiement qui fera appel a la méthode index de mon PayementController 
 
      $router->addPath(
      '/newpayement',
      'GET',
      'payement',
      PayementController::class,
      'index'
    );

et celle de l'interface d'apres paiement 

    $router->addPath(
      '/pay',
      'GET',
      'payement',
      PayementController::class,
      'Pay'
    );
    
 La fonction Pay a pour but de créer un paiement pour un utilisateur et de l'envoyer dans l'interface paid afin d'y afficher différentes inforamtions 
 
 
      public function Pay()
    {
      $facture = $this->payement->CreateUserAndPay('4242424242424242',6,2022,'314','pedro','pedro@gmail.com',50000,'eur','payement');
      echo $this->twig->render('paid.html.twig', ['facture' => $facture]);
    }
    
 
