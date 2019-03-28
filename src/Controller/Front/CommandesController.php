<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;

class CommandesController extends Controller
{
    /*
     * payement page
     */
    public function payement()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('commandes/front/payement.html.twig');
    }
    
    /*
     * livraison page
     */
    public function livraison()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('commandes/front/livraison.html.twig');
    }
}
