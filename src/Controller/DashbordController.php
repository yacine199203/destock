<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashbordController extends AbstractController
{
    /**
     * @Route("/dashbord/", name="dashbord")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepo): Response
    {
        $manager=$this->getDoctrine()->getManager();
        $users= $userRepo->findAll();
        $Tcommercial=0;
        foreach($users as $user){
            foreach($user->getRoles() as $commercial)
            if($commercial == 'ROLE_USER'){
                $Tcommercial++;
            }
        }

        return $this->render('dashbord/index.html.twig', [
            'commercial'=>$Tcommercial
        ]);
    }
}
