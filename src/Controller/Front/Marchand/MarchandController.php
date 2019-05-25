<?php

namespace App\Controller\Front\Marchand;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Favoris;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MarchandController extends Controller
{
    /*
     * store list
     */
    public function listStore()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marchand = $dm->getRepository('App:Marchands')->findOneBy(array('user' => $this->getUser()));
        $stores = $dm->getRepository('App:Stores')->findBy(array('marchand' => $marchand));
        return $this->render('marchand/storesList.html.twig', array('stores' => $stores));
    }
    
    /*
     * store infos page
     */
    public function infosStore(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $form = $this->createForm('App\Form\StoreType', $store);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($store);
            $em->flush();
            $this->addFlash('success','Votre infos sont enregistrés');
            
            return $this->redirectToRoute('marchand_stores_details', array('id' => $store->getId()));
        }

        return $this->render('marchand/infosStore.html.twig', array(
            'store' => $store,
            'form' => $form->createView(),
        ));
    }
}
