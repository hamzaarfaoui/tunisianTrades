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
     * Product page
     */
    public function productPage($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $product->setNbrView($product->getNbrView()+1);
        $dm->persist($product);
        $dm->flush();
        return $this->render('Products/front/details.html.twig', array(
            'product' => $product
        ));
    }
    
    /*
     * Product by category
     */
    public function productByCategory($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $categorie = $dm->getRepository('App:SousCategories')->find($id);
        $products = $dm->getRepository('App:Products')->findBy(array('sousCategorie' => $categorie));
        $products_price = array();
        foreach ($products as $product) {
            $products_price[] = $product->getPrice();
        }
        $min = min($products_price);
        $max = max($products_price);
        return $this->render('frontend/productsByCategory.html.twig', array(
            'products' => $products,
            'categorie' => $categorie,
            'min' => $min,
            'max'=>$max
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
                        $result[] = $product;
                    }
                }
            }
        }
        return $this->render('frontend/searchResult.html.twig', array(
            'products' => $result
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
