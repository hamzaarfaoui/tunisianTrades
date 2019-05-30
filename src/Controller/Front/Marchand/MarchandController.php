<?php

namespace App\Controller\Front\Marchand;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Favoris;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Document\AdressesStore;
use App\Document\TelephonesStore;

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
    
    /*
     * store add adress
     */
    public function addAdressStore(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($request->get('store'));
        $adresseStore = new AdressesStore();
        $adresseStore->setRue($request->get('adress'));
        $adresseStore->setStore($store);
        $store->addAdresseStore($adresseStore);
        $dm->persist($store);
        $dm->persist($adresseStore);
        $dm->flush();
        return new JsonResponse(array(
            'status' => 'OK',
            'message' => 'Adresse '.$adresseStore->getRue().' ajouté',
            'adress' => $this->renderView('marchand/partials/single_adress.html.twig', array('adress' => $adresseStore))
            ));
        
    }
    
    /*
     * store edit adress
     */
    public function editAdressStore(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $adresseStore = $dm->getRepository('App:AdressesStore')->find($id);
        $adresseStore->setRue($request->get('adress'));
        $dm->persist($adresseStore);
        $dm->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Adresse '.$adresseStore->getRue().' enregistrée'));
        
    }
    
    /*
     * store delete adress
     */
    public function deleteAdressStore(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $adresseStore = $dm->getRepository('App:AdressesStore')->find($id);
        $dm->remove($adresseStore);
        $dm->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'Adresse '.$adresseStore->getRue().' supprimé'));
        
    }
    
    /*
     * store add phone
     */
    public function addPhoneStore(Request $request)
    {
         $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($request->get('store'));
        $phoneStore = new TelephonesStore();
        $phoneStore->setNumero($request->get('phone'));
        $phoneStore->setStore($store);
        $store->addTelephoneStore($phoneStore);
        $dm->persist($store);
        $dm->persist($phoneStore);
        $dm->flush();
        return new JsonResponse(array(
            'status' => 'OK',
            'message' => 'Phone '.$phoneStore->getNumero().' ajouté',
            'phone' => $this->renderView('marchand/partials/single_phone.html.twig', array('phone' => $phoneStore))
            ));
    }
    
    /*
     * store edit phone
     */
    public function editPhoneStore(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $phoneStore = $dm->getRepository('App:TelephonesStore')->find($id);
        $phoneStore->setNumero($request->get('phone'));
        $dm->persist($phoneStore);
        $dm->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'phone '.$phoneStore->getNumero().' enregistrée'));
    }
    
    /*
     * store delete phone
     */
    public function deletePhoneStore(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $phoneStore = $dm->getRepository('App:TelephonesStore')->find($id);
        $dm->remove($phoneStore);
        $dm->flush();
        return new JsonResponse(array('status' => 'OK', 'message' => 'phone '.$phoneStore->getNumero().' supprimé'));
        
    }
}
