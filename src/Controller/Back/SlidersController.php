<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Sliders;
use App\Document\SousSliders;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;

class SlidersController extends Controller
{
    /*
     * Sliders list
     */
    public function listAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $sliders = $dm->getRepository('App:Sliders')->findAll();
        return $this->render('Sliders/list.html.twig', array('sliders' => $sliders));
    }
    
    /*
     * CategorieMere details
     */
    public function showAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $slider = $dm->getRepository('App:Sliders')->find($id);
        $product = $dm->getRepository('App:Products')->find($slider->getProduct()->getId());
        return $this->render('Sliders/show.html.twig', array('slider' => $slider, 'product'=>$product));
    }
    
    /*
     * New CategorieMere page
     */
    public function newAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        return $this->render('Sliders/new.html.twig',array());
    }
    
    /*
     * New CategorieMere traitement
     */
    public function newTraitementAction(Request $request)
    {
        
        $dm = $this->get('doctrine_mongodb')->getManager();
        $slider = new Sliders();
        $product = $dm->getRepository('App:Products')->find($request->get('product'));
        $slider->setProduct($product);
        $slider->setOrdre($request->get('ordre'));
        if($request->get('status')){
           $slider->setStatus(1);
        }else{
            $slider->setStatus(0);
        }
        if (isset($_FILES["imageSlider"]) && !empty($_FILES["imageSlider"])) {
            $file = $_FILES["imageSlider"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["imageSlider"]["tmp_name"], $this->getParameter('images_sliders') . "/" . $fileName
            );
            $slider->setImage($fileName);
        }
        $dm->persist($slider);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Nouvelle slider pour le produit ".$product->getName());
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * CategorieMere edit
     */
    public function editAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $slider = $dm->getRepository('App:Sliders')->find($id);
        return $this->render('Sliders/edit.html.twig', array('slider' => $slider));
    }
    
    /*
     * Slider edit traitement
     */
    public function editTraitementAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $slider = $dm->getRepository('App:Sliders')->find($id);
        $product = $dm->getRepository('App:Products')->find($request->get('product'));
        $slider->setProduct($product);
        $slider->setOrdre($request->get('ordre'));
        if($request->get('status')){
           $slider->setStatus(1);
        }else{
            $slider->setStatus(0);
        }
        if (isset($_FILES["imageSlider"]["name"]) && !empty($_FILES["imageSlider"]["name"])) {
            $file = $_FILES["imageSlider"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["imageSlider"]["tmp_name"], $this->getParameter('images_sliders') . "/" . $fileName
            );
            $slider->setImage($fileName);
        }else{
            $slider->setImage($slider->getImage());
        }
        $dm->persist($slider);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La slider du produit ".$product->getName()." a été modifiée");
        return $this->redirectToRoute('dashboard_product_details', array('id' => $product->getId()));
    }
    
    /*
     * Delete sliderMere
     */
    public function deleteAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $slider = $dm->getRepository('App:Sliders')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_sliders')."/".$slider->getImage(), ''.$slider->getImage().''));
        $dm->remove($slider);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La slider est supprimée");
        $referer = $request->headers->get('referer');

return $this->redirect($referer);
    }
}
