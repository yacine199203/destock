<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * @Route("/dashbord/categorie", name="category")
     */
    public function showCategory(CategoryRepository $categoryRepo): Response
    {
        $categorys = $categoryRepo->findAll();//drop-down nos produits
        return $this->render('category/index.html.twig', [
            'categorys' => $categorys, //drop-down nos produits
        ]);
    }

    /**
     * permet d'ajouter une catégorie 
     * @Route("/dashbord/ajouter-categorie", name="addCategory")
     * @return Response
     */
    public function addCategory(CategoryRepository $categoryRepo,Request $request)
    {
        $addCategory = new Category();
        $addCatForm = $this->createForm(CategoryType::class,$addCategory);
        $addCatForm-> handleRequest($request);
        if($addCatForm->isSubmitted() && $addCatForm->isValid() && empty($addCatForm->get('description')->getData()))
        {
            $file= $addCatForm->get('image')->getData();
            if($file != null)
            {
                $fileName=  uniqid().'.'.$file->guessExtension();
                if($file->guessExtension()!='png'){
                    $this->addFlash(
                        'danger',
                        "votre image doit être en format png "
                    ); 
                }else
                {
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addCategory->setImage($fileName);
                }
            }
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($addCategory); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie ".$addCategory->getCategoryName()." a bien été ajoutée "
            );
            return $this-> redirectToRoute('category');
            
        }
        
        return $this->render('category/addCategory.html.twig', [
            'addCatForm'=> $addCatForm->createView(),
        ]);
    }
}
