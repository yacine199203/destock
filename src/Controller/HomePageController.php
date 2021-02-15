<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\JobRepository;
use App\Repository\PriceRepository;
use App\Repository\SliderRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\JobProductRepository;
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
        $i=0;
        foreach($datap as $count)
        {
            if($count->getStatu()== false)
            {
                $i++;
            }
        }
        $data = $paginator->paginate(
            $datap,$request->query->getInt('page',1),
            6
        );
        return $this->render('/categoryProductList.html.twig', [
            'category'=> $category,
            'data'=> $data,
            'totalProd'=> $i,
        ]);
    }

    /**
     * permet de voir la présentation du produit
     * @Route("/categorie/{slug}/{productSlug}", name="productPresentation")
     * 
     * @return Response
     */
    public function showProductPresentation($slug,$productSlug,PriceRepository $priceRepo,CategoryRepository $categoryRepo,ProductRepository $productRepo)
    {
        $category = $categoryRepo->findOneBySlug($slug);
        $product=$productRepo->findOneBySlug($productSlug);
        $prices= $priceRepo->findOneByProduct($product->getId());
      
        return $this->render('/productPresentation.html.twig', [
            'category'=> $category,
            'product'=> $product,
            'prices'=> $prices,
        ]);
    }

    /**
     * permet de voir la présentation du produit
     * @Route("/categorie/{slug}/{productSlug}/dim", name="dim")
     * 
     * @return Response
     */
    public function dim($slug,$productSlug,PriceRepository $priceRepo,CategoryRepository $categoryRepo,ProductRepository $productRepo)
    {
        $category = $categoryRepo->findOneBySlug($slug);
        $product=$productRepo->findOneBySlug($productSlug);
        $prices= $priceRepo->findByProduct($product->getId());
        $t=[];
       
        foreach($prices as $price)
        {
            $t[]=[
                'dim'=>$price->getDimension(),
                'price1'=> number_format($price->getPrice1(), 2, ',', ' '),
                'price2'=>number_format($price->getPrice2(), 2, ',', ' '),
                'price3'=>number_format($price->getPrice3(), 2, ',', ' '),
            ];
        }
     
        return $this->json(['code'=> 200, 'message'=>'prix selon dim',
        'data'=>  $t,'prod'=> $product->getId()],200);
    }

    /**
     * permet de voir la liste des produits dans un métier
     * @Route("/metier/{slug}", name="jobProduct")
     * 
     * @return Response
     */
    public function showJobProduct($slug,JobRepository $jobRepo,JobProductRepository $jpRepo,ProductRepository $productRepo,Request $request, PaginatorInterface $paginator)
    {
        $jobs = $jobRepo->findOneBySlug($slug);
        $jps = $jpRepo->findByJob($jobs->getId());
        $products =$productRepo->findByStatu(false);
        $i=0;
        foreach($products as $count)
        {
            if($count->getStatu()== false)
            {
                $i++;
            }
        }
        $data = $paginator->paginate(
            $products,$request->query->getInt('page',1),
            6
        );
        return $this->render('jobProductList.html.twig', [
            'jobs'=> $jobs,
            'jps'=> $jps,
            'products'=> $products,
            'data'=> $data,
            'totalProd'=> count($products),
            
        ]);
    }

    /**
     * permet de voir le résultat d'une recherche
     * @Route("/recherche-'{search}'", name="searchResult")
     * 
     * @return Response
     */
    public function searchResult($search,ProductRepository $productRepo)
    {
        $manager = $this->getDoctrine()->getManager();
        $result = $manager->createQuery('SELECT p FROM App\Entity\Product p WHERE p.productName LIKE \'%'.$search.'%\'')->getResult();
        return $this->render('/search.html.twig', [
            'result'=> $result,
            'search'=> $search,
            
        ]);
    }

    /**
     * permet de voir la réalisations
     * @Route("/Nos-realisations/{slug}", name="realisations")
     * 
     * @return Response
     */
    public function realisations($slug,JobRepository $jobRepo)
    {
        $realisation = $jobRepo->findOneBySlug($slug);
        
        return $this->render('/nosRealisations.html.twig', [
            'realisation'=> $realisation, 
        ]);
    }

    
    
}
