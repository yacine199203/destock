<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(): Response
    {
        return $this->render('home_page/index.html.twig', [
        ]);
    }

    /**
     * @Route("/dashbord/", name="dashbord")
     */
    public function test(): Response
    {
        return $this->render('dashbord/dashbord.html.twig', [
        ]);
    }
}
