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

class MembersController extends Controller
{   
   
    
    /*
     * Members list
     */
    public function liste()
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $members = $dm->getRepository('App:User')->findBy(array('owner' => $this->getUser()));
        return $this->render('marchand/members/index.html.twig', array('members' => $members));
    }
    
    /*
     * Member details
     */
    public function details($id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $member = $dm->getRepository('App:User')->find($id);
        return $this->render('marchand/members/show.html.twig', array(
            'member' => $member
        ));
    }
    
    /*
     * New Member
     */
    public function newMember(Request $request)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $member = new User();
        $form = $this->createForm('App\Form\UserType', $member);
        $form->handleRequest($request);
        $users = $dm->getRepository('App:User')->findAll();
        $users_username = array();
        $users_email = array();
        foreach ($users as $user){
            $users_username [] = $user->getUsername();
            $users_email [] = $user->getEmail();
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if(in_array($form['username']->getData(), $users_username, true)){
                $request->getSession()->getFlashBag()->add('danger', "Le username ".$form['username']->getData()." est déja utilisé");
                return $this->redirectToRoute('marchand_members_new');
            }elseif (in_array($form['email']->getData(), $users_email, true)) {
                $request->getSession()->getFlashBag()->add('danger', "L'email ".$form['email']->getData()." est déja utilisé");
                return $this->redirectToRoute('marchand_members_new');
            }else{
                $options = [
                    'cost' => 11,
                    //'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
                ];
                $pass = $form['password']->getData();
                $password = password_hash($pass, PASSWORD_BCRYPT, $options);
                $member->setPassword($password);
                $member->setOwner($this->getUser());
                $member->addRole('ROLE_MEMBER_STORE');
                $member->setCreatedAt(new \DateTime('now'));
                $dm->persist($member);
                $dm->flush();
                $request->getSession()->getFlashBag()->add('success', "Le membre ".$member->getNom()." ".$member->getPrenom()." est ajouté");
                return $this->redirectToRoute('marchand_members_details', array('id' => $member->getId())); 
            }
            
        }
        return $this->render('marchand/members/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    
    /*
     * Member edit
     */
    public function editMember(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $member = $dm->getRepository('App:User')->find($id);
        $form = $this->createForm('App\Form\UserEditType', $member);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member->setUpdatedAt(new \DateTime('now'));
            $dm->persist($member);
            $dm->flush();
            $request->getSession()->getFlashBag()->add('success', "Le membre ".$member->getNom()." ".$member->getPrenom()." est mis à jour");
            return $this->redirectToRoute('marchand_members_details', array('id' => $member->getId()));
        }
        return $this->render('marchand/members/edit.html.twig', array(
            'member' => $member,
            'form' => $form->createView(),
                ));
    }
    
    /*
     * Product delete
     */
    public function deleteMember(Request $request, $id)
    {
        $dm = $this->get('doctrine_mongodb')->getManager();
        $member = $dm->getRepository('App:User')->find($id);
        $dm->remove($member);
        $dm->flush();
        $request->getSession()->getFlashBag()->add('success', "Le membre ".$member->getNom()." ".$member->getPrenom()." est supprimé");
        return $this->redirectToRoute('marchand_members_index', array('id' => $this->getUser()->getId()));
    }
}
