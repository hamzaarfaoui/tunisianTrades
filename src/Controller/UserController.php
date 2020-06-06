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
        $nombreCmdValide = $dm->getRepository('App:Commandes')->nombreCmdValide();
        $nombreCmdEnCours = $dm->getRepository('App:Commandes')->nombreCmdEnCours();
        $nombreVisite = $dm->getRepository('App:MySite')->getVisites();
        return $this->render('user/espaces/admin.html.twig',array(
            'nombre_commandes_valide' => $nombreCmdValide,
            'nombre_commandes_en_cours' => $nombreCmdEnCours,
            'nombre_visite' => $nombreVisite
        ));
    }
    
    /*
     * Employe
     */
    public function employe()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('user/espaces/employe.html.twig');
    }
    
    /*
     * Marchand
     */
    public function marchand()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        if(in_array('ROLE_MARCHAND', $this->getUser()->getRoles(), true)){
            $marchand = $dm->getRepository('App:Marchands')->findOneBy(array('user' => $this->getUser()));
        }else{
            $marchand = $dm->getRepository('App:Marchands')->findOneBy(array('user' => $this->getUser()->getOwner()));
        }
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
