<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\UpdatePass;
use App\Form\EditUserType;
use App\Form\UpdatePassType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/connexion", name="login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $userName = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'error' => $error !== null,
            'userName' => $userName,
        ]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout(): Response
    {
    }

    /**
     * @Route("/dashbord/utilisateurs", name="user")

     */
    public function index(UserRepository $userRepo): Response
    {
   
        $users = $userRepo->findAll();
        return $this->render('/account/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * permet d'ajouter un utilisateur 
     * @Route("/dashbord/ajouter-utilisateur", name="addUser")

     * @return Response
     */
    public function addUser(Request $request,UserPasswordEncoderInterface $encoder)
    {
        $addUser = new User();
        $addUserForm = $this->createForm(UserType::class,$addUser);
        $addUserForm-> handleRequest($request);
        if($addUserForm->isSubmitted() && $addUserForm->isValid() && empty($addUserForm->get('description')->getData()))
        {
            $manager=$this->getDoctrine()->getManager();
            $pass = $encoder->encodePassword($addUser, $addUser->getPass());
           
            $addUser->setPass($pass);
            $manager->persist($addUser); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur <strong>".$addUser->getFirstName()." ".$addUser->getLastName()."</strong> a bien été ajouté"
            );
            return $this-> redirectToRoute('user');
        }   
        return $this->render('account/addUser.html.twig', [
            'addUserForm'=> $addUserForm->createView(),
        ]);
    }

    /**
     * permet de modifier un utilisateur 
     * @Route("/dashbord/modifier-utilisateur/{id}/{slug}", name="editUser")

     * @return Response
     */
    public function editUser($id,$slug,UserRepository $userRepo,Request $request)
    {
        $editUser = $userRepo->findOneByid($id);
        $editUserForm = $this->createForm(EditUserType::class,$editUser);
        $editUserForm-> handleRequest($request);
        if($editUserForm->isSubmitted() && $editUserForm->isValid() && empty($editUserForm->get('description')->getData()))
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($editUser); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'utilisateur ".$editUser->getFirstName()." ".$editUser->getLastName()." a bien été modifié"
            );
            return $this-> redirectToRoute('user');
        }  
        return $this->render('account/editUser.html.twig', [
            'editUser' => $editUser,
            'editUserForm'=> $editUserForm->createView(),
        ]);
    }

    /**
     * permet de modifier le mot de passe utilisateur 
     * @Route("/dashbord/modifier-mot-de-passe", name="updatePass")
  
     * @return Response
     */
    public function updatePass(UserRepository $userRepo,Request $request,UserPasswordEncoderInterface $encoder)
    {
        $user= $this->getUser();
        $updatePass= new UpdatePass();
        $updatePassForm = $this->createForm(UpdatePassType::class,$updatePass);
        $updatePassForm-> handleRequest($request);
        if($updatePassForm->isSubmitted() && $updatePassForm->isValid())
        {
            if (!password_verify($updatePass->getOldPass(), $user->getPass()))
            {
                $updatePassForm->get('oldPass')->addError(new FormError("votre ancien mot de passe est incorrect")); 
            }else
            {
                $newPass= $updatePass->getNewPass();
                $pass= $encoder->encodePassword($user, $newPass);
                $user->setPass($pass);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($user); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this-> redirectToRoute('user');
            }
            
        }   
        return $this->render('account/updatePass.html.twig', [
            'updatePassForm'=> $updatePassForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un utilisateur
     * @Route("/dashbord/supprimer-utilisatuer/{id} ", name="removeUser")
 
     * @return Response
     */
    public function removeUser($id,UserRepository $userJobRepo)
    {   
        $removeUser = $userJobRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeUser); 
        $manager->flush();
        $this->addFlash(
            'success',
            "L'utilisateur <strong>".$removeUser->getFirstName()." ".$removeUser->getLastName()."</strong> a bien été supprimé"
        );
        return $this-> redirectToRoute('user');
    }
}
