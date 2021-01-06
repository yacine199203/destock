<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Component\Form\FormError;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{

    /**
     * permet de voir la liste des catégories
     * @Route("/dashbord/categories", name="category")
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
            if($file != null && $file->guessExtension()!='png')
            {
                $addCatForm->get('image')->addError(new FormError("votre image doit être en format png")); 
            }else
            {
                if($file != null && $file->guessExtension()=='png')
                {
                    $fileName=  uniqid().'.'.$file->guessExtension();
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addCategory->setImage($fileName);
                }
               
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($addCategory); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "La catégorie <strong>".$addCategory->getCategoryName()."</strong> a bien été ajoutée "
                );
                return $this-> redirectToRoute('category');  
            }
        }
        
        return $this->render('category/addCategory.html.twig', [
            'addCatForm'=> $addCatForm->createView(),
        ]);
    }

    /**
     * permet de modifier une catégorie
     * @Route("/dashbord/modifier-categorie/{categoryName} ", name="editCategory")
     * @return Response
     */
    public function editCategory($categoryName,CategoryRepository $categoryRepo,Request $request)
    {   
        $editCategory = $categoryRepo->findOneBySlug($categoryName);
        $editCatForm = $this->createForm(CategoryType::class,$editCategory);
        $editCatForm-> handleRequest($request);
        if($editCatForm->isSubmitted() && $editCatForm->isValid() && empty($editCatForm->get('description')->getData()))
        {
            $file= $editCatForm->get('image')->getData();
            if($file != null && $file->guessExtension()!='png')
            {
                $editCatForm->get('image')->addError(new FormError("votre image doit être en format png")); 
            }else
            {
                if($file != null && $file->guessExtension()=='png')
                {
                    $unlinkFile= $editCategory->getImage();
                    unlink('../public/images/'.$unlinkFile);
                    $fileName=  uniqid().'.'.$file->guessExtension();
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $editCategory->setImage($fileName);
                }
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($editCategory); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "La catégorie <strong>".$editCategory->getCategoryName()."</strong> a bien été modifiée"
                );
                return $this-> redirectToRoute('category');  
            }
        }
        return $this->render('category/editCategory.html.twig', [
            'editCatForm'=> $editCatForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une catégorie
     * @Route("/dashbord/supprimer-categorie/{categoryName} ", name="removeCategory")
     * @return Response
     */
    public function removeCategory($categoryName,CategoryRepository $categoryRepo)
    {   
        $removeCategory = $categoryRepo->findOneBySlug($categoryName);
        $file= $removeCategory->getImage();
        if($removeCategory->getImage() != null){
            unlink('../public/images/'.$file);
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeCategory); 
        $manager->flush();
            $this->addFlash(
                'success',
                "La catégorie <strong> ".$removeCategory->getCategoryName()."</strong> a bien été supprimée "
            );
            return $this-> redirectToRoute('category');
    }
}
