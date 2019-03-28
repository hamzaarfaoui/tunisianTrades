<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;

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
    public function productPage()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('Products/front/details.html.twig');
    }
    
    /*
     * Product by category
     */
    public function productByCategory($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $categorie = $dm->getRepository('App:SousCategories')->find($id);
        $products = $dm->getRepository('App:Products')->findBy(array('sousCategorie' => $categorie));
        return $this->render('frontend/productsByCategory.html.twig', array(
            'products' => $products,
            'categorie' => $categorie
        ));
    }
    
    /*
     * search result
     */
    public function searchResult()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('frontend/searchResult.html.twig');
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
