<?php

namespace App;

abstract class PaymentModule 
{

    public function __construct()
    {
        
    }

    abstract public function createUtilisateurFromCreditCardAndReturnUser($cardNumber,$expMonth,$expYear,$cvc,$name,$email);
    abstract public function createUserAndPay($cardNumber,$expMonth,$expYear,$cvc,$name,$email,$amount,$currency,$description);
    abstract public function createSubscription($NameoftheSubscription,$amount,$interval,$currency);
    abstract public function createToken($cardNumber,$expMonth,$expYear,$cvc);
    abstract public function creatCustomer($token,$name,$email);
    abstract public function createCharge($amount,$currency,$description,$token);
    abstract public function createProduct($name);
    abstract public function listAllProducts();
    abstract public function createPlan($amount,$interval,$currency,$name,$idPlan);
    abstract public function subscribetoPlan($idPlan,$token);
}
