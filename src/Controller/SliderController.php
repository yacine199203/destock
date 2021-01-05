<?php

namespace App\Controller;

use App\Entity\Slider;
use App\Form\SliderType;
use App\Repository\SliderRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SliderController extends AbstractController
{
    /**
     * permet de voir la liste des catégories
     * @Route("/dashbord/slider", name="slider")
     */
    public function showCategory(SliderRepository $slidersRepo): Response
    {
        $sliders = $slidersRepo->findAll();
        return $this->render('slider/index.html.twig', [
            'sliders' => $sliders,
        ]);
    }

    /**
     * permet d'ajouter un slider
     * @Route("/dashbord/ajouter-slid", name="addSlid")
     * @return Response
     */
    public function addSlid(Request $request)
    {
        $addSlider = new Slider();
        $addSlidForm = $this->createForm(SliderType::class,$addSlider);
        $addSlidForm-> handleRequest($request);
        if($addSlidForm->isSubmitted() && $addSlidForm->isValid() && empty($addSlidForm->get('description')->getData()))
        {
            $file= $addSlidForm->get('image')->getData();
            if($file == null)
            {
                
                $addSlidForm->get('image')->addError(new FormError("Le champ Image est vide")); 
            }else
            {
                $fileName=  md5(uniqid()).'.'.$file->guessExtension();
                if($file->guessExtension()!='png'){ 
                    $addSlidForm->get('image')->addError(new FormError("votre image doit être en format png")); 
                }else{
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $addSlider->setImage($fileName);
                    $manager=$this->getDoctrine()->getManager();
                    $manager->persist($addSlider); 
                    $manager->flush();
                    $this->addFlash(
                        'success',
                        "Le slid a bien été ajouté "
                    );
                    return $this-> redirectToRoute('slider');
                } 
            }
        }
        return $this->render('slider/addSlid.html.twig', [
            'addSlidForm'=> $addSlidForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un slid
     * @Route("/dashbord/supprimer-slid/{image} ", name="removeSlid")
     * @return Response
     */
    public function removeSlid($image,SliderRepository $slidersRepo)
    {   
        $removeslid = $slidersRepo->findOneById($image);
        $file= $removeslid->getImage();
        unlink('../public/images/'.$file);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeslid); 
        $manager->flush();
        $this->addFlash(
            'success',
            "Le slid a bien été supprimé "
        );
        return $this-> redirectToRoute('slider');
    }
}
