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
        }
        $categorie = $dm->getRepository('App:SousCategories')->find($request->get('categorie'));
        $query['categorie'] = $categorie;
        $query['minimum'] = intval($request->get('min'));
        $query['maximum'] = intval($request->get('max'));
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
        foreach ($products as $product){
            
            
            if($product->getSousCategorie()->getId() == $categorie->getId()){
                if(in_array($product->getMarque(), $marques)){
                    if(!in_array($product, $products_list)){
                        $products_list[] = $product;
                    }
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
