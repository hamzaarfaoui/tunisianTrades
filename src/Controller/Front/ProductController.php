<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;

class ProductController extends Controller
{
    /*
     * Products list Front
     */
    public function listFrontAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $products = $dm->getRepository('App:Products')->findAll();
        return $this->render('Products/front/list.html.twig', array('products' => $products));
    }
}
