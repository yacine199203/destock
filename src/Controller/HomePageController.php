<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\SliderRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomePageController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     */
    public function index(Request $request,SliderRepository $slidersRepo): Response
    {
        $sliders = $slidersRepo->findAll();
        $newsletter = new Newsletter();
        $newsletterForm = $this->createForm(NewsletterType::class,$newsletter);
        $newsletterForm-> handleRequest($request);
        if($newsletterForm->isSubmitted() && $newsletterForm->isValid() && empty($newsletterForm->get('description')->getData()))
        {
            $manager=$this->getDoctrine()->getManager();
            $newsletter->setStatus(false);
            $newsletter->setUnsubscribe(false);
            $manager->persist($newsletter); 
            $manager->flush();
            return $this-> redirectToRoute('home_page');
        }   
        return $this->render('/home_page/index.html.twig', [
            'sliders' => $sliders,
            'newsletterForm'=> $newsletterForm->createView(),
        ]);
    }

    /***************************************************************************************************/

    /**
     * permet de voir la liste des produits dans une catégorie
     * @Route("/categorie/{slug}", name="categoryproduct")
     * 
     * @return Response
     */
    public function showCategoryProduct($slug,CategoryRepository $categoryRepo,Request $request, PaginatorInterface $paginator)
    {
        $category = $categoryRepo->findOneBySlug($slug);
        $datap= $category->getProducts();
        $data = $paginator->paginate(
            $datap,$request->query->getInt('page',1),
            6
        );
        return $this->render('/categoryProductList.html.twig', [
            'category'=> $category,
            'data'=> $data,
        ]);
    }
}
