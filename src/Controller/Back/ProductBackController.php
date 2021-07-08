<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Products;
use App\Entity\User;
use App\Entity\Marchands;
use App\Entity\AdressesStore;
use App\Entity\AdressesUser;
use App\Entity\TelephonesUser;
use App\Entity\TelephonesStore;
use App\Entity\MediasImages;
use App\Entity\Promotions;
use App\Entity\Keywords;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\DirectoryType;

class ProductBackController extends Controller
{   
    /*
     * Products list
     */
    public function liste(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $find_products = $dm->getRepository('App:Products')->findAll();
        $paginator  = $this->get('knp_paginator');
        $products = $paginator->paginate(
            $find_products, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $products->setTemplate('Products/back/pagination.html.twig');
        return $this->render('Products/back/list.html.twig', array('products' => $products));
    }
    
    /*
     * Products list
     */
    public function search(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $search = $request->get('recherche');
        $disponible = null;
        $request->get('disponible')?$disponible='oui':'non'; 
        $find_products = $dm->getRepository('App:Products')->byQB($search, $disponible);
        $paginator  = $this->get('knp_paginator');
        $keywords = $dm->getRepository('App:Keywords')->byName($search);
        $result = array();
        foreach ($find_products as $fp){
            $result[] = $fp; 
        }
        if(count($result) == 0){
            foreach ($keywords as $k){
                if(in_array($k, $keys)){
                    if(!in_array($k->getProduct(), $result)){
                        $result[] = $product;
                    }
                }
            }
        }
        $products = $paginator->paginate(
            $result, /* query NOT result */
            $request->query->get('page', 1), /*page number*/
            10 /*limit per page*/
        );
        $products->setTemplate('Products/back/pagination.html.twig');
        return $this->render('Products/back/list.html.twig', array('products' => $products));
    }
    
    /*
     * Products list by store
     */
    public function listeByStore($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $products = $dm->getRepository('App:Products')->findBy(array('store' => $store));
        return $this->render('Products/back/listByStore.html.twig', array('products' => $products, 'store' => $store));
    }
    
    /*
     * Product details
     */
    public function details($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $banner = $dm->getRepository('App:Banners')->findOneBy(array('product' => $product));
        $slider = $dm->getRepository('App:Sliders')->findOneBy(array('product' => $product));
        $sliders = $dm->getRepository('App:Sliders')->findAll();
        return $this->render('Products/back/details.html.twig', array(
            'product' => $product
            , 'banner' => $banner,
            'slider' => $slider,
            'sliders' => $sliders
                ));
    }
    
    /*
     * New Product page
     */
    public function newAction()
    {
        $dm = $this->getDoctrine()->getManager();
        $categoriesMere = $dm->getRepository('App:CategoriesMere')->findAll();
        $sousCategories1 = $dm->getRepository('App:Categories')->findAll();
        $caracteristiques = $dm->getRepository('App:Caracteristiques')->findAll();
        $sousCategories2 = $dm->getRepository('App:SousCategories')->findAll();
        $stores = $dm->getRepository('App:Stores')->findAll();
        return $this->render('Products/back/new.html.twig', array(
            'categoriesMere' => $categoriesMere,
            'caracteristiques' => $caracteristiques,
            'stores' => $stores,
            'sousCategories1' => $sousCategories1,
            'sousCategories2' => $sousCategories2
        ));
    }
    
    /*
     * New Store Product page
     */
    public function newByStoreAction($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $categoriesMere = $dm->getRepository('App:CategoriesMere')->findAll();
        $sousCategories1 = $dm->getRepository('App:Categories')->findAll();
        $caracteristiques = $dm->getRepository('App:Caracteristiques')->findAll();
        $sousCategories2 = $dm->getRepository('App:SousCategories')->findAll();
        $stores = $dm->getRepository('App:Stores')->findAll();
        return $this->render('Products/back/newByStore.html.twig', array(
            'store' => $store,
            'categoriesMere' => $categoriesMere,
            'caracteristiques' => $caracteristiques,
            'stores' => $stores,
            'sousCategories1' => $sousCategories1,
            'sousCategories2' => $sousCategories2
        ));
    }
    
    /*
     * New Product by store traitement
     */
    public function newTraitementByStoreAction(Request $request, $id)
    {
        $dm = $this->getDoctrine()->getManager();
        $product = new Products();
        $product->setName($request->get('nom'));
        $product->setPrice($request->get('price'));
        $product->setQte($request->get('qte'));
        $product->setContent($request->get('descriptionC'));
        $store = $dm->getRepository('App:Stores')->find($id);
        $store->addProduct($product);
        $product->setStore($store);
        $slug = preg_replace('/[^A-Za-z0-9. -]/', '', $request->get('nom'));

        // Replace sequences of spaces with hyphen
        $slug = preg_replace('/  */', '-', $slug);

        // The above means "a space, followed by a space repeated zero or more times"
        // (should be equivalent to / +/)

        // You may also want to try this alternative:
        $slug = preg_replace('/\\s+/', '-', $slug);
        $p = $dm->getRepository('App:Products')->findOneBy(array('slug'=>$slug));
        if($product){$slug = $slug.rand(1,25412).'-'.rand(1,2541222).$request->get('price').$request->get('qte');}
        $product->setSlug($slug);
        $product->setNbrAddToCart(0);
        $product->setNbrView(0);
        $product->setNbrAddToFavorite(0);
        $dm->persist($store);
        if($request->get('marque')){
        $marque_id = $request->get('marque');
        $marque = $dm->getRepository('App:Marques')->find($marque_id);
        $product->setMarque($marque);
        }
        if($request->get('sc')){
            $sc = $dm->getRepository('App:SousCategories')->find($request->get('sc'));
            $product->setSousCategorie($sc);
        }
        /*start medias Images document*/
        if (isset($_FILES["images"]['name']) && !empty($_FILES["images"]['name'])) {
            for ($count = 0; $count < count($_FILES["images"]["name"]); $count++) {
                if(isset($_FILES["images"]['name']) && !empty($_FILES['images']['name'][$count])){
                    $mediaImage = new MediasImages();
                    $file = $_FILES['images']['name'][$count];
                    $File_Ext = substr($file, strrpos($file, '.'));
                    $fileName = md5(uniqid()) . $File_Ext;
                    $path = $this->getParameter('images_products_img_gallery') . '/' . $fileName;
                    move_uploaded_file($_FILES['images']['tmp_name'][$count], $path);
                    $mediaImage->setName($fileName);
                    $mediaImage->setProduct($product);
                    $dm->persist($mediaImage);
                }
            }
        }
        /*end medias Images document*/
        if (isset($_FILES["iconeC"]['name']) && !empty($_FILES["iconeC"]['name'])) {
            $file = $_FILES["iconeC"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["iconeC"]["tmp_name"], $this->getParameter('images_products_img') . "/" . $fileName
            );
            $product->setImage($fileName);
        }
        /*start promotion document*/
        if($request->get('datedebut') && $request->get('datefin') && $request->get('fixe')){
            $promotion = null;
            if($request->get('promotion')){
                $promotion = $dm->getRepository('App:Promotions')->find($request->get('promotion'));
            }else{
                $promotion = new Promotions(); 
                $promotion->setProduct($product);
            }

            $promotion->setDebut(new \DateTime(''.$request->get('datedebut').''));
            $promotion->setFin(new \DateTime(''.$request->get('datefin').''));
            $promotion->setFixe($request->get('fixe'));
            $promotion->setCreatedAt(new \DateTime('now'));
            $product->setPricePromotion($request->get('fixe'));
            $dm->persist($promotion);
        }
        /*end promotion document*/
        /*start Caractéristique valeur document*/
        $valeurs = $dm->getRepository('App:Valeurs')->findAll();
        foreach ($valeurs as $valeur){
            if($valeur->getId() == $request->get('valeur'.$valeur->getCaracteristique()->getId())){
                $product->addValeur($valeur);
                $valeur->addProduct($product);
                $dm->persist($valeur);
            }
        }
        /*start keywords*/
        $keywords_input = $request->get('keywords');
        $keywords_array = explode(",", $keywords_input);
        foreach ($keywords_array as $item) {
            $keyword = new Keywords();
            $keyword->setName($item);
            $keyword->setProduct($product);
            $keyword->setCategorie($product->getSousCategorie());
            $dm->persist($keyword);
            $product->addKeyword($keyword);
        }
        
        /*end kewords*/
        /*start Caractéristique valeur document*/
        $dm->persist($product);
        /*end store document*/
        
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le produit ".$product->getName()." a été ajoutée");
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * New Product traitement
     */
    public function newTraitementAction(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $product = new Products();
        $product->setName($request->get('nom'));
        $product->setPrice($request->get('price'));
        $product->setQte($request->get('qte'));
        $slug = preg_replace('/[^A-Za-z0-9. -]/', '', $request->get('nom'));

        // Replace sequences of spaces with hyphen
        $slug = preg_replace('/  */', '-', $slug);

        // The above means "a space, followed by a space repeated zero or more times"
        // (should be equivalent to / +/)

        // You may also want to try this alternative:
        $slug = preg_replace('/\\s+/', '-', $slug);
        $p = $dm->getRepository('App:Products')->findOneBy(array('slug'=>$slug));
        if($product){$slug = $slug.rand(1,25412).'-'.rand(1,2541222).$request->get('price').$request->get('qte');}
        $product->setSlug($slug);
        $product->setContent($request->get('descriptionC'));
        if($request->get('marque')){
        $marque_id = $request->get('marque');
        $marque = $dm->getRepository('App:Marques')->find($marque_id);
        $product->setMarque($marque);
        }
        
        $product->setNbrAddToCart(0);
        $product->setNbrView(0);
        $product->setNbrAddToFavorite(0);
        if($request->get('store')){
            $store = $dm->getRepository('App:Stores')->find($request->get('store'));
            $store->addProduct($product);
            $product->setStore($store);
            $dm->persist($store);
        }
        
        if($request->get('sc')){
            $sc = $dm->getRepository('App:SousCategories')->find($request->get('sc'));
            $product->setSousCategorie($sc);
        }
        /*start medias Images document*/
        if (isset($_FILES["images"]['name']) && !empty($_FILES["images"]['name'])) {
            for ($count = 0; $count < count($_FILES["images"]["name"]); $count++) {
                if(isset($_FILES["images"]['name']) && !empty($_FILES['images']['name'][$count])){
                
                    $mediaImage = new MediasImages();
                    $file = $_FILES['images']['name'][$count];
                    $File_Ext = substr($file, strrpos($file, '.'));
                    $fileName = md5(uniqid()) . $File_Ext;
                    $path = $this->getParameter('images_products_img_gallery') . '/' . $fileName;
                    move_uploaded_file($_FILES['images']['tmp_name'][$count], $path);
                    $mediaImage->setName($fileName);
                    $mediaImage->setProduct($product);
                    $dm->persist($mediaImage);
                }
            }
        }
        /*end medias Images document*/
        if (isset($_FILES["iconeC"]['name']) && !empty($_FILES["iconeC"]['name'])) {
            $file = $_FILES["iconeC"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["iconeC"]["tmp_name"], $this->getParameter('images_products_img') . "/" . $fileName
            );
            $product->setImage($fileName);
        }
        if($request->get('datedeb') && $request->get('datefin') && $request->get('fixe')){
            if($request->get('promotion')){
                $promotion = $dm->getRepository('App:Promotions')->find($request->get('promotion'));
            }else{
                $promotion = new Promotions(); 
                $promotion->setProduct($product);
            }

            $promotion->setDebut(new \DateTime(''.$request->get('datedebut').''));
            $promotion->setFin(new \DateTime(''.$request->get('datefin').''));
            $promotion->setFixe($request->get('fixe'));
            $promotion->setUpdatedAt(new \DateTime('now'));
            $product->setPricePromotion($request->get('fixe'));
            $dm->persist($promotion);
        }
        /*end promotion document*/
        /*start Caractéristique valeur document*/
        $valeurs = $dm->getRepository('App:Valeurs')->findAll();
        foreach ($valeurs as $valeur){
            if($valeur->getId() == $request->get('valeur'.$valeur->getCaracteristique()->getId())){
                $product->addValeur($valeur);
            }
        }
        /*start keywords*/
        $keywords_input = $request->get('keywords');
        $keywords_array = explode(",", $keywords_input);
        foreach ($keywords_array as $item) {
            $keyword = new Keywords();
            $keyword->setName($item);
            $keyword->setProduct($product);
            $keyword->setCategorie($product->getSousCategorie());
            $dm->persist($keyword);
            $product->addKeyword($keyword);
        }
        
        /*end kewords*/
        /*start Caractéristique valeur document*/
        $dm->persist($product);
        /*end store document*/
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le produit ".$product->getName()." a été ajoutée");
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * Product edit
     */
    public function editAction($id)
    {
        $dm = $this->getDoctrine()->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $promotion = $dm->getRepository('App:Promotions')->findOneBy(array('product' => $product));
        $categoriesMere = $dm->getRepository('App:CategoriesMere')->findAll();
        $sousCategories1 = $dm->getRepository('App:Categories')->findAll();
        $caracteristiques = $dm->getRepository('App:Caracteristiques')->findBy(array('sousCategorie' => $product->getSousCategorie()));
        $sousCategories2 = $dm->getRepository('App:SousCategories')->findAll();
        $marques = $dm->getRepository('App:Marques')->findAll();
        $stores = $dm->getRepository('App:Stores')->findAll();
        return $this->render('Products/back/edit.html.twig', array(
            'product' => $product,
            'categoriesMere' => $categoriesMere,
            'caracteristiques' => $caracteristiques,
            'promotion' => $promotion,
            'stores' => $stores,
            'sousCategories1' => $sousCategories1,
            'sousCategories2' => $sousCategories2,
            'marques' => $marques
                ));
    }
    
    /*
     * Edit Product traitement
     */
    public function editTraitementAction(Request $request, $id)
    {
        $dm = $this->getDoctrine()->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $product->setName($request->get('nom'));
        $product->setPrice($request->get('price'));
        $product->setPricePromotion($request->get('price'));
        $product->setQte($request->get('qte'));
        $product->setContent($request->get('descriptionC'));
        $marque_id = $request->get('marque');
        $marque = $dm->getRepository('App:Marques')->find($marque_id);
        $product->setMarque($marque);
        $p = $dm->getRepository('App:Products')->find($id);
        if($request->get('store')){
            $store = $dm->getRepository('App:Stores')->find($request->get('store'));
            $store->addProduct($product);
            $product->setStore($store);
            $dm->persist($store);
        }
        
        if($request->get('sc')){
            $sc = $dm->getRepository('App:SousCategories')->find($request->get('sc'));
            $product->setSousCategorie($sc);
        }
        /*start medias Images document*/
        if (isset($_FILES["images"]['name']) && !empty($_FILES["images"]['name'])) {
            foreach ($product->getMediasImages() as $img){
                $dm->remove($img);
            }
            for ($count = 0; $count < count($_FILES["images"]["name"]); $count++) {
                
                $mediaImage = new MediasImages();
                $file = $_FILES['images']['name'][$count];
                $File_Ext = substr($file, strrpos($file, '.'));
                $fileName = md5(uniqid()) . $File_Ext;
                $path = $this->getParameter('images_products_img_gallery') . '/' . $fileName;
                move_uploaded_file($_FILES['images']['tmp_name'][$count], $path);
                $mediaImage->setName($fileName);
                $mediaImage->setProduct($product);
                $dm->persist($mediaImage);
            }
        }
        /*end medias Images document*/
        if (isset($_FILES["iconeC"]['name']) && !empty($_FILES["iconeC"]['name'])) {
            $file = $_FILES["iconeC"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["iconeC"]["tmp_name"], $this->getParameter('images_products_img') . "/" . $fileName
            );
            $product->setImage($fileName);
        }
        /*start promotion document*/
        if($request->get('datedeb') && $request->get('datefin') && $request->get('fixe') && $request->get('haspromotion') == 1){
            if($request->get('promotion')){
                $promotion = $dm->getRepository('App:Promotions')->find($request->get('promotion'));
            }else{
                $promotion = new Promotions(); 
                $promotion->setProduct($product);
            }

            $promotion->setDebut(new \DateTime(''.$request->get('datedebut').''));
            $promotion->setFin(new \DateTime(''.$request->get('datefin').''));
            $promotion->setFixe($request->get('fixe'));
            $promotion->setUpdatedAt(new \DateTime('now'));
            $product->setPricePromotion($request->get('fixe'));
            $dm->persist($promotion);
        }
        if($request->get('haspromotion') == 0){
            $promotion = $dm->getRepository('App:Promotions')->findOneBy(array('product' => $product));
            $dm->remove($promotion);
            $dm->flush();
        }
        /*end promotion document*/
        /*start Caractéristique valeur document*/
        $valeurs = $dm->getRepository('App:Valeurs')->findAll();
        foreach ($valeurs as $valeur){
            if($valeur->getId() == $request->get('valeur'.$valeur->getCaracteristique()->getId())){
                $product->addValeur($valeur);
                $valeur->addProduct($product);
                $dm->persist($valeur);
            }
        }
        
        /*start keywords*/
        $keywords_input = $request->get('keywords');
        $keywords_array = explode(",", $keywords_input);
        $keywords_product = $dm->getRepository('App:Keywords')->findBy(array('product'=>$product));
        foreach ($keywords_product as $k){
            $product->removeKeyword($k);
            $dm->remove($k);
        }
        foreach ($keywords_array as $item) {
                $keyword = new Keywords();
                $keyword->setName($item);
                $keyword->setProduct($product);
                $keyword->setCategorie($product->getSousCategorie());
                $dm->persist($keyword);
                $product->addKeyword($keyword);
        }
        
        /*end kewords*/
        /*start Caractéristique valeur document*/
        $dm->persist($product);
        /*end store document*/
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le produit ".$product->getName()." a été modifié");
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * Product delete
     */
    public function deleteAction(Request $request, $id)
    {
        $dm = $this->getDoctrine()->getManager();
        $fileSystem = new Filesystem();
        $product = $dm->getRepository('App:Products')->find($id);
        $sliders = $dm->getRepository('App:Sliders')->findBy(array('product' => $product));
        $banners = $dm->getRepository('App:Banners')->findBy(array('product' => $product));
        $promotions = $dm->getRepository('App:Promotions')->findBy(array('product' => $product));
        $keywords = $product->getKeywords();
        $valeurs = $product->getValeurs();
        $images = $product->getMediasImages();
        $videos = $product->getMediasVideos();
        foreach ($keywords as $keyword) {
            $product->removeKeyword($keyword);
            $dm->remove($keyword);
        }
        foreach ($valeurs as $valeur) {
            $product->removeValeur($valeur);
            $valeur->removeProduct($product);
        }
        foreach ($videos as $video) {
            $product->removeMediasVideo($video);
            $dm->remove($video);
        }
        foreach ($promotions as $promotion) {
            $dm->remove($promotion);
        }
        foreach ($sliders as $slider) {
            $dm->remove($slider);
        }
        foreach ($banners as $banner) {
            $dm->remove($banner);
        }
        $fileSystem->remove(array('symlink', $this->getParameter('images_products_img')."/".$product->getImage(), ''.$product->getImage().''));
        foreach ($product->getMediasImages() as $image){
            $fileSystem->remove(array('symlink', $this->getParameter('images_products_img_gallery')."/".$image->getName(), ''.$image->getName().''));
            $dm->remove($image);
        }
        $dm->remove($product);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le produit ".$product->getName()." est supprimé");
        return $this->redirectToRoute('dashboard_product_index');
    }
    
    /*
     * Products order in categorie
     */
    public function productOrderInCategorie(Request $request)
    {
        $dm = $this->getDoctrine()->getManager();
        $listItem = $request->request->get('listItem');
        $limit = $request->request->get('limit');
        $page = $request->request->get('page');
        $count = 1;

        foreach ($listItem as $item) {
            $position = (($page - 1) * $limit) + $count;
            $product = $dm->getRepository('App:Products')->find($item);
            $product->setPosition($position);
                $dm->persist($product);
                $count++;
        }

        $dm->flush();

        return new JsonResponse([
            'listItem' => $listItem,
            'limit' => $limit,
            'page' => $page
        ]);

    }
}
