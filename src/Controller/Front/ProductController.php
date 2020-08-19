<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Products;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /*
     * Products list Front
     */
    public function listFrontAction()
    {
        $dm = $this->getDoctrine()->getManager();
        $products = $dm->getRepository('App:Products')->findAll();
        return $this->render('Products/front/list.html.twig', array('products' => $products));
    }
    
    /*
     * New products
     */
    public function newProducts(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $find_products = $dm->getRepository('App:Products')->newProducts();
        $paginator  = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $find_products, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('frontend/newProducts.html.twig', array('products' => $products));
    }
    
    /*
     * Products in promotion
     */
    public function productsInPromotion(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $find_products = $dm->getRepository('App:Products')->newProducts();
        $paginator  = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $find_products, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('frontend/productsInPromotion.html.twig', array('products' => $products));
    }
    
    /*
     * Products Tri
     */
    public function triAction(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $query = array();
        $caracteristiques = [];
        $marques = [];
        if(!empty($request->get('caracteristiques'))){
            foreach ($request->get('caracteristiques') as $item){
                $valeur = $dm->getRepository('App:Valeurs')->find($item);
                $caracteristiques[] = $valeur->getId();
            }
        }
        if(!empty($request->get('marques'))){
            foreach ($request->get('marques') as $item){
                $marque = $dm->getRepository('App:Marques')->find($item);
                $marques[] = $marque;
            }
            $query['marques'] = $marques;
        }
        if(!empty($request->get('categorie'))){
            $categorie = $dm->getRepository('App:SousCategories')->find($request->get('categorie'));
            $query['categorie'] = $categorie->getId();
        }elseif(!empty($request->get('categories'))){
            $query['categories'] = $request->get('categories');
        }
        
        $query['minimum'] = $request->get('min')?intval($request->get('min')):null;
        $query['maximum'] = $request->get('max')?intval($request->get('max')):null;
        $query['tri'] = $request->get('valeur');
        if(!empty($request->get('store'))){
            $query['store'] = $request->get('store');
        }
        if (count($caracteristiques) >= 1) {$query['valeurs'] = $caracteristiques;}
        $products = $dm->getRepository('App:Products')->byCategorie($query);
        $products_list = array();
        foreach ($products as $product){
            $price = $product->getPricePromotion()?$product->getPricePromotion():$product->getPrice();

            if($request->get('min') && !empty($request->get('min')) && $request->get('max') && !empty($request->get('max'))){
                if($price <= intval($request->get('min'))){
                    $products_list[] = $product;
                }
            }
            if($request->get('max') && !empty($request->get('max'))){
                if($price <= intval($request->get('max'))){
                    $products_list[] = $product;
                }
            }
        }
        
        
        if(count($products_list)==0){$products_list=$products;}
        
        return new JsonResponse(array(
            'status' => 'OK',
            'range' => '('.intval($query['minimum']).') --- ('.intval($query['maximum']).') : '. count($products_list).' produits',
            'c' => $query['tri'],
            'nbr' => count($products_list),
            'message' => 'Tout est bon',
            'products' => $this->renderView('frontend/partials/triProduct.html.twig', array('products' => $products_list))
        ));
    }
}
