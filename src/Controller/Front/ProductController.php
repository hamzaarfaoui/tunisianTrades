<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends Controller
{
    /*
     * Products list Front
     */
    public function listFrontAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $products = $dm->getRepository('App:Products')->findAll();
        return $this->render('Products/front/list.html.twig', array('products' => $products));
    }
    
    /*
     * Products Tri
     */
    public function triAction(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $query = array();
        $caracteristiques = [];
        if(!empty($request->get('caracteristiques'))){
            foreach ($request->get('caracteristiques') as $item){
                $valeur = $dm->getRepository('App:Valeurs')->find($item);
                $caracteristiques[] = $valeur->getId();
            }
        }
        
        

        $categorie = $dm->getRepository('App:SousCategories')->find($request->get('categorie'));
        
        $query['categorie'] = $categorie;
        $query['tri'] = $request->get('valeur');
        if (count($caracteristiques) >= 1) {$query['valeurs'] = $caracteristiques;}
        $products = $dm->getRepository('App:Products')->byCategorie($query);
        $products_list = array();
        foreach ($products as $product){
            $valeurs_id = array();
            foreach ($product->getValeurs() as $v){
                $valeurs_id[] = $v->getId();
            }
            if($product->getSousCategorie()->getId() == $categorie->getId()){
                foreach ($caracteristiques as $caracteristique){
                    if(in_array($caracteristique, $valeurs_id)){
                        $products_list[] = $product;
                    }
                }
                
            }
            
        }
//        if($request->get('valeur')==0){
//            $products = $dm->getRepository('App:Products')->byPriceDesc($query);
//        }elseif($request->get('valeur')==1){
//            $products = $dm->getRepository('App:Products')->byPriceAsc($query);
//        }else{
//            $products = $dm->getRepository('App:Products')->byPulaires($query);
//        }
        
        return new JsonResponse(array(
            'status' => 'OK',
            'c' => $query,
            'ca' => $valeurs_id,
            'nbr' => count($products_list),
            'message' => 'Tout est bon',
            'products' => $this->renderView('frontend/partials/triProduct.html.twig', array('products' => $products_list))
        ));
    }
}
