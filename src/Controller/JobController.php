<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JobController extends AbstractController
{
   
    /**
     * permet de voir les métiers
     * @Route("/dashbord/metiers", name="job")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(JobRepository $jobRepo): Response
    {
        $jobs=$jobRepo->findAll();
        return $this->render('job/index.html.twig', [
            'jobs'=>$jobs,
        ]);
    }

    /**
     * permet d'ajouter un métier 
     * @Route("/dashbord/ajouter-metier", name="addJob")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function addJob(Request $request)
    {
        $addJob = new Job();
        $addJobForm = $this->createForm(JobType::class,$addJob);
        $addJobForm-> handleRequest($request);
        if($addJobForm->isSubmitted() && $addJobForm->isValid() && empty($addJobForm->get('description')->getData()))
        {
            $file= $addJobForm->get('image')->getData();
            if($file == null)
            {
                $addJobForm->get('image')->addError(new FormError("ce champ est vide"));
            }else
            {
                if($file != null && $file->guessExtension()!='png')
                {
                    $addJobForm->get('image')->addError(new FormError("votre image doit être en format png")); 
                }else
                {
                    if($file != null && $file->guessExtension()=='png')
                    {
                        $fileName=  uniqid().'.'.$file->guessExtension();
                        $file->move($this->getParameter('upload_directory_png'),$fileName);
                        $addJob->setImage($fileName);
                    }
                   
                    $manager=$this->getDoctrine()->getManager();
                    $manager->persist($addJob); 
                    $manager->flush();
                    $this->addFlash(
                        'success',
                        "Le métier <strong>".$addJob->getJobName()."</strong> a bien été ajouté "
                    );
                    return $this-> redirectToRoute('job');  
                }
            }
        }
        return $this->render('job/addJob.html.twig', [
            'addJobForm'=> $addJobForm->createView(),
        ]);
    }

    /**
     * permet de modifier un métier 
     * @Route("/dashbord/modifier-metier/{id}", name="editJob")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editJob($id,JobRepository $jobRepo,Request $request)
    {
        $editJob = $jobRepo->findOneById($id);
        $editJobForm = $this->createForm(JobType::class,$editJob);
        $editJobForm-> handleRequest($request);
        if($editJobForm->isSubmitted() && $editJobForm->isValid() && empty($editJobForm->get('description')->getData()))
        {
            $file= $editJobForm->get('image')->getData();
            
            if($file != null && $file->guessExtension()!='png')
            {
                $editJobForm->get('image')->addError(new FormError("votre image doit être en format png")); 
            }else
            {
                if($file != null && $file->guessExtension()=='png')
                {
                    $unlinkFile= $editJob->getImage();
                    unlink('../public/images/'.$unlinkFile);
                    $fileName=  uniqid().'.'.$file->guessExtension();
                    $file->move($this->getParameter('upload_directory_png'),$fileName);
                    $editJob->setImage($fileName);
                }
                   
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($editJob); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le métier <strong>".$editJob->getJobName()."</strong> a bien été modifié "
                );
                return $this-> redirectToRoute('job');  
            }
        }
        return $this->render('job/editJob.html.twig', [
            'editJobForm'=> $editJobForm->createView(),
        ]);
    }

    /**
     * permet de supprimer un métier
     * @Route("/dashbord/supprimer-metier/{id} ", name="removeJob")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeCategory($id,JobRepository $jobRepo)
    {   
        $removeJob = $jobRepo->findOneById($id);
        $file= $removeJob->getImage();
        if($removeJob->getImage() != null){
            unlink('../public/images/'.$file);
        }
        foreach($removeJob->getRealisations() as $realisations)
        {
            foreach($realisations->getRealisationImages() as $realisation)
            {
                $file= $realisation->getImage();
                unlink('../public/images/'.$file);
            }
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeJob); 
        $manager->flush();
            $this->addFlash(
                'success',
                "Le métier <strong> ".$removeJob->getJobName()."</strong> a bien été supprimé "
            );
            return $this-> redirectToRoute('job');
    }
}
