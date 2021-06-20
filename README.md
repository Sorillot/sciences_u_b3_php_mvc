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
 
  `
  use Stripe;
  use Stripe\Plan;
  use Stripe\Stripe as StripeStripe;
  `
 
## Fonctionnalité

Présentation des différentes fonctionnalité installée.

