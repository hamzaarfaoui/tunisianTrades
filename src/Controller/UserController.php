<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;

class UserController extends Controller
{
    /*
     * Dashboard
     */
    public function dashboard()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('user/espaces/admin.html.twig');
    }
    
    /*
     * Marchand
     */
    public function marchand()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marchand = $dm->getRepository('App:Marchands')->findOneBy(array('user' => $this->getUser()));
        $store = $dm->getRepository('App:Stores')->findOneBy(array('marchand' => $marchand));
        return $this->render('user/espaces/marchand.html.twig',array('store' => $store));
    }
    
    /*
     * client
     */
    public function client()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('user/espaces/client.html.twig');
    }
}
