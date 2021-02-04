<?php

namespace App\Controller;

use App\Entity\Recrut;
use App\Form\RecrutType;
use App\Repository\RecrutRepository;
use App\Repository\ProfilRecrutRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecrutController extends AbstractController
{
    /**
     * @Route("/recrutement", name="recrut")
     * 
     */
    public function index(RecrutRepository $recruitementRepo): Response
    {
        return $this->render('recrut/index.html.twig', [
            'recruitement' => $recruitementRepo->findAll(),
        ]);
    }

     /**
     * voir recrutement
     * @Route("/dashbord/recrutement", name="showRecrut")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showRec(RecrutRepository $recruitementRepo): Response
    {
        $recruitement = $recruitementRepo->findAll();
        return $this->render('/recrut/showRecrut.html.twig', [
            'recruitement' => $recruitement,
        ]);
    }

    /**
     * permet d'ajouter une offre d'emploi 
     * @Route("/dashbord/ajouter-offre-d-emploi", name="addRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function addRec(Request $request)
    {
        $addRec = new Recrut();
        $addRecForm = $this->createForm(RecrutType::class,$addRec);
        $addRecForm-> handleRequest($request);
        if($addRecForm->isSubmitted() && $addRecForm->isValid() && empty($addRecForm->get('description')->getData()))
        {
            $manager=$this->getDoctrine()->getManager();
            foreach ($addRec->getProfilRecruts() as $cond)
                {
                    $cond->setPoste($addRec);
                    $manager->persist($cond);
                }
            $manager->persist($addRec); 
            $manager->flush();
            $this->addFlash(
                'success',
                "l'offre d'emploi <strong>".$addRec->getPoste()."</strong> a bien été ajoutée"
            );
            return $this-> redirectToRoute('showRecrut');
        }   
        return $this->render('recrut/addRecrut.html.twig', [
            'addRecForm'=> $addRecForm->createView(),
        ]);
    }

    /**
     * permet de modifier une offre d'emploi 
     * @Route("/dashbord/modifier-offre-d-emploi/{id}", name="editRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editRec($id,RecrutRepository $recruitementRepo,Request $request)
    {
        $editRec = $recruitementRepo->findOneById($id);
        $editRecForm = $this->createForm(RecrutType::class,$editRec);
        $editRecForm-> handleRequest($request);
        if($editRecForm->isSubmitted() && $editRecForm->isValid() && empty($editRecForm->get('description')->getData()))
        {
            $manager=$this->getDoctrine()->getManager();
            foreach ($editRec->getProfilRecruts() as $cond)
                {
                    $cond->setPoste($editRec);
                    $manager->persist($cond);
                }
            $manager->persist($editRec); 
            $manager->flush();
            $this->addFlash(
                'success',
                "L'offre d'emploi <strong>".$editRec->getPoste()."</strong> a bien été modifiée"
            );
            return $this-> redirectToRoute('showRecrut');
        }   
        return $this->render('recrut/editRecrut.html.twig', [
            'editRecForm'=> $editRecForm->createView(),
        ]);
    }

    /**
     * permet de supprimer une offre d'emploi
     * @Route("/dashbord/supprimer-offre-d-emploi/{id} ", name="removeRec")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeRec($id,RecrutRepository $recruitementRepo)
    {   
        $removeRec = $recruitementRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeRec); 
        $manager->flush();
            $this->addFlash(
                'success',
                 "L'offre d'emploi <strong>".$removeRec->getPoste()."</strong> a bien été supprimée"
            );
            return $this-> redirectToRoute('showRecrut');
    }

    /**
     * permet de supprimer une offre d'emploi
     * @Route("/recrutement/{id} ", name="cond")
     * @return Response
     */
    public function conditions($id,ProfilRecrutRepository $recruitementRepo)
    {   
        $conditions = $recruitementRepo->findByPoste($id);
        $cond=[];
        foreach($conditions as $c)
        {
            $cond []=$c->getConditions();
        }
        return $this->json(['code'=> 200, 'message'=>'conditions',
        'data'=>$cond],200);
    }
}
