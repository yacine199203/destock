<?php

namespace App\Controller;

use App\Entity\Realisation;
use App\Form\RealisationType;
use App\Entity\RealisationImages;
use Symfony\Component\Form\FormError;
use App\Repository\RealisationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\RealisationImagesRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RealisationController extends AbstractController
{
   /**
     * @Route("/realisations ", name="realisation")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(RealisationRepository $realisationRepo): Response
    {
        $realisations = $realisationRepo->findAll();
        return $this->render('/realisation/index.html.twig', [
            'realisations' => $realisations,
        ]);
    }

    /**
     * @Route("/realisations/ajouter-realisation ", name="addRealisation")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addRealisation(Request $request): Response
    {
        $addRealisation = new Realisation();
        $addRealForm = $this->createForm(RealisationType::class,$addRealisation);
        $addRealForm-> handleRequest($request);
        if($addRealForm->isSubmitted() && $addRealForm->isValid() && empty($addRealForm->get('description')->getData()))
        { 
          
            $fileImages= $addRealForm->get('image')->getData();
            if($fileImages == null)
            {
                $addRealForm->get('image')->addError(new FormError("Ce champ est vide"));
            }else
            {
                foreach($fileImages as $img)
                {
                    if($img->guessExtension()!='png')
                    {
                        $addRealForm->get('image')->addError(new FormError("vos images doivent être en format png"));
                        $this->addFlash(
                            'danger',
                            "La réalisation n'a pas été ajoutée, la galerie photo contient un fichier qui n'est pas au format png"
                        );
                        return $this-> redirectToRoute('realisation');
                    }
                }
    
                $manager=$this->getDoctrine()->getManager();
                foreach($fileImages as $image)
                {
                    // On génère un nouveau nom de fichier
                    $fileNameImage = md5(uniqid()).'.'.$image->guessExtension();
                    
                    // On copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('upload_directory_png'),
                        $fileNameImage
                    );
                        
                    // On crée l'image dans la base de données
                    $img = new RealisationImages();
                    $img->setImage($fileNameImage);
                    $addRealisation->addRealisationImage($img);
                }
                $manager->persist($addRealisation); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "La réalisation <strong>".$addRealisation->getCustomer()."</strong> a bien été ajoutée "
                );
                return $this-> redirectToRoute('realisation'); 
            }
                
        }
        
        return $this->render('realisation/addRealisation.html.twig', [
            'addRealForm'=> $addRealForm->createView(),
            
        ]);
    }

    /**
     * @Route("/realisations/modifier-realisation/{id} ", name="editRealisation")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editRealisation($id,RealisationRepository $realisationRepo,Request $request): Response
    {
        $editRealisation = $realisationRepo->findOneById($id);
        $editRealForm = $this->createForm(RealisationType::class,$editRealisation);
        $editRealForm-> handleRequest($request);
        if($editRealForm->isSubmitted() && $editRealForm->isValid() && empty($editRealForm->get('description')->getData()))
        {
            $fileImages= $editRealForm->get('image')->getData();
            if($fileImages != null)
            {
                foreach($fileImages as $image)
                {
                    if($image->guessExtension()!='png')
                        {
                            $editRealForm->get('image')->addError(new FormError("vos images doivent être en format png"));
                            $this->addFlash(
                                'danger',
                                "La réalisation n'a pas été modifié, la galerie photo contient un fichier qui n'est pas au format png"
                            );
                            return $this-> redirectToRoute('realisation');

                        }
                    $fileNameImage = md5(uniqid()).'.'.$image->guessExtension();
                    $image->move(
                        $this->getParameter('upload_directory_png'),
                        $fileNameImage
                    );
                    $img = new RealisationImages();
                    $img->setImage($fileNameImage);
                    $editRealisation->addRealisationImage($img);
                }
            }
            $manager=$this->getDoctrine()->getManager();
            $manager->persist($editRealisation); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La réalisation <strong>".$editRealisation->getCustomer()."</strong> a bien été modifiée "
            );
            return $this-> redirectToRoute('realisation');
               
        } 
        return $this->render('realisation/editRealisation.html.twig', [
            'editRealisation'=> $editRealisation,
            'editRealForm'=> $editRealForm->createView(),
        ]);
    }

    /**
    * @Route("/supprimer-images-realisation/{id}", name="removeRi")
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteImage( $id,RealisationImagesRepository $riRepo)
    {
        $removeri = $riRepo->findOneById($id);
        $file= $removeri->getImage();
        if($removeri->getImage() != null){
            unlink('../public/images/'.$file);
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeri); 
        $manager->flush();
        return $this->json(['code'=> 200, 'message'=>'image supprimée'],200);
    }

    /**
     * permet de supprimer une réalisation
     * @Route("/dashbord/supprimer-realisation/{id} ", name="removeRealisation")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeRealisation($id,RealisationRepository $realisationtRepo)
    {   
        $removeRealisation = $realisationtRepo->findOneById($id);
        foreach($removeRealisation->getRealisationImages() as $rp)
        {
            if($rp != null){
                unlink('../public/images/'.$rp->getImage());
            }
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeRealisation); 
        $manager->flush();
        $this->addFlash(
            'success',
            "La réalisation <strong>".$removeRealisation->getCustomer()."</strong> a bien été supprimée "
        );
        return $this-> redirectToRoute('realisation');
    }
}
