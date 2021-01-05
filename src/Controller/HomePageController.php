<?php

namespace App\Controller;

use App\Repository\SliderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(SliderRepository $slidersRepo): Response
    {
        $sliders = $slidersRepo->findAll();
        return $this->render('home_page/index.html.twig', [
            'sliders' => $sliders,
        ]);
    }
}
