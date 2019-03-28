<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Stores;

class StoresController extends Controller
{
    /*
     * Stores list
     */
    public function listAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $stores = $dm->getRepository('App:Stores')->findAll();
        return $this->render('stores/front/list.html.twig', array('stores' => $stores));
    }
    
    /*
     * Store details
     */
    public function showAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        return $this->render('stores/front/show.html.twig', array('store' => $store));
    }
}
