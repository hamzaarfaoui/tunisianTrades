<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Document\Marques;
use Symfony\Component\HttpFoundation\Request;

class MarquesController extends Controller
{
    /*
     * Marques list
     */
    public function listAction()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marques = $dm->getRepository('App:Marques')->findAll();
        return $this->render('marques/list.html.twig', array('marques' => $marques));
    }
    
    /*
     * Marque details
     */
    public function showAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marque = $dm->getRepository('App:Marques')->find($id);
        return $this->render('marques/show.html.twig', array('marque' => $marque));
    }
    
    /*
     * New Marque page
     */
    public function newAction()
    {
        return $this->render('marques/new.html.twig');
    }
    
    /*
     * New marque traitement
     */
    public function newTraitementAction(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marque = new Marques();
        $marque->setName($request->get('nom'));
        $marque->setContent($request->get('descriptionC'));
        $marque->setCreatedAt(new \DateTime('now'));
        $marque->setVideo($request->get('video'));
        if (isset($_FILES["image"]) && !empty($_FILES["image"])) {
            $file = $_FILES["image"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["image"]["tmp_name"], $this->getParameter('images_marques') . "/" . $fileName
            );
            $marque->setImage($fileName);
        }
        $dm->persist($marque);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La marque ".$marque->getName()." a été ajoutée");
        return $this->redirectToRoute('dashboard_marques_back_details', array('id' => $marque->getId()));
    }
    
    /*
     * Marque edit
     */
    public function editAction($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marque = $dm->getRepository('App:Marques')->find($id);
        return $this->render('marques/edit.html.twig', array('marque' => $marque));
    }
    
    /*
     * Edit Marque traitement
     */
    public function editTraitementAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $marque = $dm->getRepository('App:Marques')->find($id);
        $marque->setName($request->get('nom'));
        $marque->setContent($request->get('description'));
        $marque->setVideo($request->get('video'));
        $marque->setUpdatedAt(new \DateTime('now'));
        if (isset($_FILES["image"]["name"]) && !empty($_FILES["image"]["name"])) {
            $file = $_FILES["image"]["name"];
            $File_Ext = substr($file, strrpos($file, '.'));
            $fileName = md5(uniqid()) . $File_Ext;
            move_uploaded_file(
                    $_FILES["image"]["tmp_name"], $this->getParameter('images_marques') . "/" . $fileName
            );
            $marque->setImage($fileName);
        }
        
        $dm->persist($marque);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La marque ".$marque->getName()." a été ajoutée");
        return $this->redirectToRoute('dashboard_marques_back_details', array('id' => $marque->getId()));
    }
    
    /*
     * Delete Marque
     */
    public function deleteAction(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $fileSystem = new Filesystem();
        $marque = $dm->getRepository('App:CategoriesMere')->find($id);
        $fileSystem->remove(array('symlink', $this->getParameter('images_marques')."/".$marque->getImage(), ''.$marque->getImage().''));
        $dm->remove($marque);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "La marque ".$marque->getName()." supprimée");
        return $this->redirectToRoute('dashboard_marques_back_index');
    }
}
