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
        return $this->render('user/espaces/marchand.html.twig');
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
