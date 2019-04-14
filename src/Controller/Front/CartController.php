<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CartController extends Controller
{
    /*
     * Page panier
     */
    public function cart(Request $request)
    {
        $session = $request->getSession();
        $dm = $this->get('doctrine_mongodb')->getManager();
        if (!$session->has('panier')){$session->set('panier', array());}
        $products = $dm->getRepository('App:Products')->findArray(array_keys($session->get('panier')));
        return $this->render('frontend/cart/cart.html.twig', array(
            'products' => $products,
            'panier' => $session->get('panier')
        ));
    }
    
    /*
     * Calcul montant total et nombre de produits dans le panier
     */
    public function fetchTotalCart(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $session = $request->getSession();
        $array_product_ids = "";
        if (!$session->has('panier')){
            $articles = 0;
            $array_product_ids = [];
        }
        else{
            $articles = count($session->get('panier'));
            $array_product_ids = $session->get('panier');
        }
        if($session->get('panier') == ""){
            
        }
        $products = $dm->getRepository('App:Products')->findArray(array_keys($array_product_ids));
        $total = 0;
        foreach ($products as $product){
            $total+=$product->getPrice();
        }
        return new JsonResponse(array('status' => 'ok', 'total' => $total, 'qte' => $articles));
    }
    
    /*
     * Affichage des produits en panier dans le header
     */
    public function CartInHeader(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $session = $request->getSession();
        $array_product_ids = "";
        if (!$session->has('panier')){
            $articles = 0;
            $array_product_ids = [];
        }
        else{
            $articles = count($session->get('panier'));
            $array_product_ids = $session->get('panier');
        }
        if($session->get('panier') == ""){
            
        }
        $products = $dm->getRepository('App:Products')->findArray(array_keys($array_product_ids));
        return $this->render('frontend/cart/header.html.twig', array(
            'articles' => $articles,
            'products' => $products
        ));
    }
    
    /*
     * Ajout d'produit au panier 
     */
    public function addProductToCart(Request $request, $id)
    {
        $session = $request->getSession();
        if (!$session->has('panier')) {$session->set('panier',array());}
        $panier = $session->get('panier');
        
        if (array_key_exists($id, $panier)) {
            if ($request->query->get('qte') != null) {$panier[$id] = $request->query->get('qte');}
            $this->get('session')->getFlashBag()->add('success','Quantité modifié avec succès');
        } else {
            if ($request->query->get('qte') != null)
            {$panier[$id] = $request->query->get('qte');}
            else
            { $panier[$id] = 1;}
        }
        $session->set('panier',$panier);
        return new JsonResponse(array('status'=>'OK', 'message'=>'Produit ajouté en panier'));
    }
    
    /*
     * Retirer un produit du panier 
     */
    public function removeProductFromCart(Request $request, $id)
    {
        $session = $request->getSession();
        $panier = $session->get('panier');
        
        if (array_key_exists($id, $panier))
        {
            unset($panier[$id]);
            $session->set('panier',$panier);
        }
        return new JsonResponse(array('status'=>'OK', 'message'=>'Produit retirer du panier'));
    }
}
