<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;

class CartController extends Controller
{
    /*
     * Cart page
     */
    public function cart()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('frontend/cart.html.twig');
    }
}
