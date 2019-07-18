<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;
use Symfony\Component\HttpFoundation\Request;

class FrontController extends Controller
{
    /*
     * Index page
     */
    public function indexPage()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $sc2 = $dm->getRepository('App:SousCategories')->findBy(array('showInIndex' => 1));
        $categories = $dm->getRepository('App:CategoriesMere')->findAll();
        return $this->render('index.html.twig', array(
            'sc2' => $sc2,
            'categories' => $categories
        ));
    }
    
    /*
     * About page
     */
    public function aboutPage()
    {
        return $this->render('frontend/about.html.twig');
    }
    
    /*
     * Contact page
     */
    public function contactPage()
    {
        return $this->render('frontend/contact.html.twig');
    }
    
    /*
     * Process order page
     */
    public function processOrderPage()
    {
        return $this->render('frontend/processOrder.html.twig');
    }
    
    /*
     * Product page
     */
    public function productPage($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $query = array();
        $query['id'] = $id;
        $query['sousCategorie'] = $product->getSousCategorie();
        $products = $dm->getRepository('App:Products')->produitsLiees($query);
        $product->setNbrView($product->getNbrView()+1);
        $dm->persist($product);
        $dm->flush();
        return $this->render('Products/front/details.html.twig', array(
            'product' => $product,
            'products' => $products
        ));
    }
    
    /*
     * Product by category
     */
    public function productByCategory(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $categorie = $dm->getRepository('App:SousCategories')->find($id);
        $find_products = $dm->getRepository('App:Products')->findBy(array('sousCategorie' => $categorie));
        $paginator  = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $find_products, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );
        $marques = $dm->getRepository('App:Marques')->findBy(array('sousCategorie' => $categorie));
        $products_price = array();
        foreach ($find_products as $product) {
            $products_price[] = $product->getPrice();
        }
        $min = min($products_price);
        $max = max($products_price);
        return $this->render('frontend/productsByCategory.html.twig', array(
            'products' => $products,
            'categorie' => $categorie,
            'marques' => $marques,
            'min' => $min,
            'max'=>$max,
        ));
    }
    
    /*
     * Store market
     */
    public function store(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $products = $dm->getRepository('App:Products')->byStore($id);
        $categories = array();
        $marques = array();
        $caracteriqtiques = array();
        $products_price = array();
        $result = array();
        foreach ($products as $product){
            if(!in_array($product->getSousCategorie(), $categories)){
                $categories []= $product->getSousCategorie();
            }
            if(!in_array($product->getMarque(), $marques)){
                $marques []= $product->getMarque();
            }
            $result[] = $product;
            $products_price[] = $product->getPrice();
            
        }
        foreach ($categories as $categorie){
            foreach ($categorie->getCaracteristiques() as $c){
                if(!in_array($c, $caracteriqtiques)){
                    $caracteriqtiques []= $c;
                }
            }
        }
        $min = min($products_price);
        $max = max($products_price);
        $paginator  = $this->get('knp_paginator');
        
        $products_list = $paginator->paginate(
            $result, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('frontend/store.html.twig', array(
            'products' => $products_list,
            'categories' => $categories,
            'marques' => $marques,
            'caracteriqtiques' => $caracteriqtiques,
            'min' => $min,
            'max'=>$max,
            'store' => $store
        ));
    }
    
    /*
     * search result
     */
    public function searchResult(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $search = $request->get('search');
        $products = $dm->getRepository('App:Products')->findAll();
        $keywords = $dm->getRepository('App:Keywords')->byName($search);
        $result = array();
        foreach ($products as $product){
            $keys = $dm->getRepository('App:Keywords')->findBy(array('product' => $product));
            if(count($product->getKeywords())>0){
                foreach ($keywords as $k){
                    if(in_array($k, $keys)){
                        if(!in_array($product, $result)){
                            $result[] = $product;
                        }
                    }
                }
            }
        }
        $paginator  = $this->get('knp_paginator');
        $products_list = $paginator->paginate(
            $result, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            20 /*limit per page*/
        );
        return $this->render('frontend/searchResult.html.twig', array(
            'products' => $products_list,
            'search' => $search
        ));
    }
    
    /*
     * other filters result
     */
    public function otherFiltersResult()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('frontend/otherFiltersResult.html.twig');
    }
}
