<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Banners;
use App\Document\SousBanners;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

class BannersController extends Controller
{
    /*
     * Banners list
     */
    public function listAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $banners = $dm->getRepository('App:Banners')->findAll();
        return $this->render('Banners/list.html.twig', array('banners' => $banners));
    }
    
    /*
     * CategorieMere details
     */
    public function showAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $banner = $dm->getRepository('App:Banners')->find($id);
        $product = $dm->getRepository('App:Products')->find($banner->getProduct()->getId());
        return $this->render('Banners/show.html.twig', array('banner' => $banner, 'product'=>$product));
    }
    
    /*
     * New CategorieMere page
     */
    public function newAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('Banners/new.html.twig',array());
    }
    
    /*
     * New CategorieMere traitement
     */
    public function newTraitementAction(Request $request)
    {
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $banner = new Banners();
        $product = $dm->getRepository('App:Products')->find($request->get('product'));
        $banner->setProduct($product);
        if($request->get('status')){
           $banner->setStatus(1);
        }else{
            $banner->setStatus(0);
        }
        if (isset($_FILES["image"]) && !empty($_FILES["image"])) {
            $file = $_FILES["image"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["image"]["tmp_name"], $this->getParameter('images_banners') . "/" . $fileName
            );
            $banner->setImage($fileName);
        }
        $dm->persist($banner);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Nouvelle banniére pour le produit ".$product->getName());
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * CategorieMere edit
     */
    public function editAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $banner = $dm->getRepository('App:Banners')->find($id);
        return $this->render('Banners/edit.html.twig', array('banner' => $banner));
    }
    
    /*
     * CategorieMere edit traitement
     */
    public function editTraitementAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $banner = $dm->getRepository('App:Banners')->find($id);
        $product = $dm->getRepository('App:Products')->find($request->get('product'));
        $banner->setProduct($product);
        if($request->get('status')){
           $banner->setStatus(1);
        }else{
            $banner->setStatus(0);
        }
        if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
            $file = $_FILES["image"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["image"]["tmp_name"], $this->getParameter('images_banners') . "/" . $fileName
            );
            $banner->setImage($fileName);
        }else{
            $banner->setImage($banner->getImage());
        }
        $dm->persist($banner);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La banniére du produit ".$product->getName()." a été modifiée");
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * Delete bannerMere
     */
    public function deleteAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $banner = $dm->getRepository('App:Banners')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_banners')."/".$banner->getImage(), ''.$banner->getImage().''));
        $dm->remove($banner);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La banniére est supprimée");
        $referer = $request->headers->get('referer');

return $this->redirect($referer);
    }
}
