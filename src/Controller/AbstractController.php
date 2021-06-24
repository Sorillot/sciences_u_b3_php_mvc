<?php


namespace App\Controller;

use App\Payement;
use Twig\Environment;

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