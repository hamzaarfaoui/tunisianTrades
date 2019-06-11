<?php

namespace App\Controller\Front\Marchand;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Products;
use App\Document\User;
use App\Document\Marchands;
use App\Document\AdressesStore;
use App\Document\AdressesUser;
use App\Document\TelephonesUser;
use App\Document\TelephonesStore;
use App\Document\MediasImages;
use App\Document\Promotions;
use App\Document\Keywords;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductsController extends Controller
{   
   
    
    /*
     * Products list
     */
    public function liste($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $products = $dm->getRepository('App:Products')->findBy(array('store' => $store));
        return $this->render('Products/marchand/index.html.twig', array('products' => $products, 'store' => $store));
    }
    
    /*
     * Products list in dashbord
     */
    public function listeInDash($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $productsByNbrViews = $dm->getRepository('App:Products')->byNbrViews($store);
        $productsByNbrAddToCart = $dm->getRepository('App:Products')->byNbrAddToCart($store);
        $productsByNbrAddToFavorite = $dm->getRepository('App:Products')->byNbrAddToFavorite($store);
        return $this->render('Products/marchand/dash.html.twig', array(
            'productsByNbrViews'=>$productsByNbrViews,
            'productsByNbrAddToCart' => $productsByNbrAddToCart,
            'productsByNbrAddToFavorite' => $productsByNbrAddToFavorite,
            'store' => $store
                ));
    }
    
    /*
     * Product details
     */
    public function details($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $banner = $dm->getRepository('App:Banners')->findOneBy(array('product' => $product));
        $slider = $dm->getRepository('App:Sliders')->findOneBy(array('product' => $product));
        $sliders = $dm->getRepository('App:Sliders')->findAll();
        return $this->render('Products/marchand/show.html.twig', array(
            'product' => $product
            , 'banner' => $banner,
            'slider' => $slider,
            'sliders' => $sliders
                ));
    }
    
    /*
     * New Product page
     */
    public function newProduct($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $store = $dm->getRepository('App:Stores')->find($id);
        $categoriesMere = $dm->getRepository('App:CategoriesMere')->findAll();
        $sousCategories1 = $dm->getRepository('App:Categories')->findAll();
        $caracteristiques = $dm->getRepository('App:Caracteristiques')->findAll();
        $sousCategories2 = $dm->getRepository('App:SousCategories')->findAll();
        $marques = $dm->getRepository('App:Marques')->findAll();
        $stores = $dm->getRepository('App:Stores')->findAll();
        $products = $dm->getRepository('App:Products')->findBy(array('store'=>$store));
        return $this->render('Products/marchand/new.html.twig', array(
            'store' => $store,
            'categoriesMere' => $categoriesMere,
            'caracteristiques' => $caracteristiques,
            'stores' => $stores,
            'sousCategories1' => $sousCategories1,
            'sousCategories2' => $sousCategories2,
            'marques' => $marques
        ));
    }
    
    /*
     * New Product traitement
     */
    public function newnewProductTraitement(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = new Products();
        $product->setName($request->get('nom'));
        $product->setPrice($request->get('price'));
        $product->setQte($request->get('qte'));
        $product->setContent($request->get('descriptionC'));
        $store = $dm->getRepository('App:Stores')->find($id);
        $store->addProduct($product);
        $product->setStore($store);
        $product->setNbrAddToCart(0);
        $product->setNbrView(0);
        $product->setNbrAddToFavorite(0);
        if($request->get('marque')){
        $marque_id = $request->get('marque');
        $marque = $dm->getRepository('App:Marques')->find($marque_id);
        $product->setMarque($marque);
        }
        $dm->persist($store);
        if($request->get('sc')){
            $sc = $dm->getRepository('App:SousCategories')->find($request->get('sc'));
            $product->setSousCategorie($sc);
        }
        /*start medias Images document*/
        if (isset($_FILES["images"]['name']) && !empty($_FILES["images"]['name'])) {
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
        if (isset($_FILES["iconeC"]) && !empty($_FILES["iconeC"])) {
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
        return $this->redirectToRoute('marchand_product_details', array('id' => $product->getId()));
    }
    
    
    /*
     * Product edit
     */
    public function editAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $promotion = $dm->getRepository('App:Promotions')->findOneBy(array('product' => $product));
        $medias = $dm->getRepository('App:MediasImages')->findBy(array('product' => $product));
        $categoriesMere = $dm->getRepository('App:CategoriesMere')->findAll();
        $sousCategories1 = $dm->getRepository('App:Categories')->findAll();
        $caracteristiques = $dm->getRepository('App:Caracteristiques')->findAll();
        $sousCategories2 = $dm->getRepository('App:SousCategories')->findAll();
        $marques = $dm->getRepository('App:Marques')->findAll();
        $stores = $dm->getRepository('App:Stores')->findAll();
        return $this->render('Products/marchand/edit.html.twig', array(
            'product' => $product,
            'categoriesMere' => $categoriesMere,
            'caracteristiques' => $caracteristiques,
            'promotion' => $promotion,
            'stores' => $stores,
            'medias'=>$medias,
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
        $dm = $this->get('doctrine_mongodb')->getManager();
        $product = $dm->getRepository('App:Products')->find($id);
        $product->setName($request->get('nom'));
        $product->setPrice($request->get('price'));
        $product->setQte($request->get('qte'));
        $product->setContent($request->get('descriptionC'));
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
        if (isset($_FILES["iconeC"]) && empty($_FILES["iconeC"])) {
            $file = $_FILES["iconeC"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["iconeC"]["tmp_name"], $this->getParameter('images_products_img') . "/" . $fileName
            );
            $product->setImage($fileName);
        }
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
        return $this->redirectToRoute('marchand_product_details', array('id' => $product->getId()));
    }
    
    /*
     * Product delete
     */
    public function deleteAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $product = $dm->getRepository('App:Products')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_products_img')."/".$product->getImage(), ''.$product->getImage().''));
        foreach ($product->getMediasImages() as $image){
            $fileSystem->remove(array('symlink', $this->getParameter('images_products_img_gallery')."/".$image->getName(), ''.$image->getName().''));
            $dm->remove($product);
        }
        $dm->remove($product);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le produit ".$product->getName()." est supprimé");
        return $this->redirectToRoute('marchand_product_index', array('id' => $product->getStore()->getId()));
    }
    
    /*
     * Delete product image
     */
    public function deleteProductImage(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $product = $dm->getRepository('App:Products')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_products_img')."/".$product->getImage(), ''.$product->getImage().''));
        $product->setImage(null);
        $dm->persist($product);
        $dm->flush();
        return new JsonResponse(array('message' => 'image supprimer'));
    }
    
    /*
     * Delete product image gallery
     */
    public function deleteProductImageGallery(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $image = $dm->getRepository('App:MediasImages')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_products_img_gallery')."/".$image->getName(), ''.$image->getName().''));
        $dm->remove($image);
        $dm->flush();
        return new JsonResponse(array('message' => 'image supprimer'));
    }
}
