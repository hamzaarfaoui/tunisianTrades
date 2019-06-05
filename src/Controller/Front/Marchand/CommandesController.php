<?php

namespace App\Controller\Front\Marchand;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Favoris;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Document\AdressesStore;
use App\Document\TelephonesStore;

class CommandesController extends Controller
{
    /*
     * store Commandes list
     */
    public function listCommandes($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $commandes = $dm->getRepository('App:Commandes')->liste();
        $commandes_liste = array();
        foreach ($commandes as $commande){
            foreach ($commande->getFacture()[0] as $facture){
                if($facture['id_vendeur'] == $id){
                    $commandes_liste [] = $commande;
                }
            }
        }
        die();
        return $this->render('marchand/storesList.html.twig', array('stores' => $stores));
    }
    
    /*
     * store Commandes details
     */
    public function showAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marchand = $dm->getRepository('App:Marchands')->find($id);
        $stores = $dm->getRepository('App:Stores')->findBy(array('marchand' => $marchand));
        return $this->render('marchand/storesList.html.twig', array('stores' => $stores));
    }
    
    /*
     * store Commandes list
     */
    public function valider($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marchand = $dm->getRepository('App:Marchands')->find($id);
        $stores = $dm->getRepository('App:Stores')->findBy(array('marchand' => $marchand));
        return $this->render('marchand/storesList.html.twig', array('stores' => $stores));
    }
}
