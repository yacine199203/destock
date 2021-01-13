<?php

namespace App\Controller;

use App\Form\SendMailType;
use Symfony\Component\Form\FormError;
use App\Repository\NewsletterRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NewsletterController extends AbstractController
{
    /**
     * @Route("/dashbord/newsletter", name="newsletter")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(NewsletterRepository $newsletterRepo): Response
    {
        $subscribers = $newsletterRepo->findBy(array(), array('id' => 'DESC'));
        return $this->render('/newsletter/index.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * @Route("/dashbord/newsletter/emails", name="emails")
     * @IsGranted("ROLE_ADMIN")
     */
    public function emails(NewsletterRepository $newsletterRepo): Response
    {
        
        $subscribers = $newsletterRepo->findAll();
        return $this->render('/newsletter/emails.html.twig', [
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * @Route("/dashbord/newsletter/envoyer-email", name="sendEmail")
     * @IsGranted("ROLE_ADMIN")
     */
    public function sendEmail(Request $request,\Swift_Mailer $mailer,NewsletterRepository $repo): Response
    {
        $to=$repo->findAll();
        $sendMail = $this->createForm(SendMailType::class);
        $sendMail-> handleRequest($request);
        if($sendMail->isSubmitted() && $sendMail->isValid() && empty($sendMail->get('description')->getData()))
        {
            $mail= $sendMail->getData();
            
            foreach($to as $t)
            {
                $message =(new \Swift_Message($mail['object']))
                ->setFrom('test.formation.tf@gmail.com')
                // On attribue le destinataire
                ->setTo($t->getEmail())
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'send_mail/body.html.twig', compact('mail')
                    ),
                    'text/html'
                );
                $mailer->send($message);
            }
        }
        return $this->render('/send_mail/index.html.twig', [
            'sendMail'=>$sendMail->createView(),
        ]);
    }

    /**
     * @Route("/dashbord/newsletter/{id}", name="statusEmail")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showsubscriber($id,NewsletterRepository $newsletterRepo): Response
    {
        $subscribers = $newsletterRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        if($subscribers->getStatus()==false)
        {
            $subscribers->setStatus(true);
        }
        else
        {
            $subscribers->setStatus(false);
        }  
        $manager->persist($subscribers); 
        $manager->flush(); 

        return $this-> redirectToRoute('newsletter');
    }

    /**
     * permet de supprimer un abonné
     * @Route("/dashbord/supprimer-abonne/{id} ", name="removeSubscribers")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeSubscribers($id,NewsletterRepository $newsletterRepo)
    {   
        $subscribers = $newsletterRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($subscribers); 
        $manager->flush();
            $this->addFlash(
                'success',
                 $subscribers->getName()." a bien été supprimé"
            );
            return $this-> redirectToRoute('newsletter');
    }

    /**
     * permet de supprimer tous les abonnés 
     * @Route("/dashbord/supprimer-tous-les-abonnes ", name="removeSubAll")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeAllSuscr(NewsletterRepository $newsletterRepo)
    {  
        $subscribers = $newsletterRepo->findBy(array(), array('id' => 'DESC'));
        $manager=$this->getDoctrine()->getManager();
        foreach ($subscribers as $sub)
        {
            $remove= $sub->getUnsubscribe();
            if($remove != false )
            {
                $manager->remove($sub); 
            }
        }
        $manager->flush();
            $this->addFlash(
                'success',
                 "Abonnés supprimés"
            );
            return $this-> redirectToRoute('newsletter');
    }

    /**
     * permet de se désabonner de la newsletter
     * @Route("/se-desabonner", name="unsubscribeNewsletter")
     */
    public function showNewsletter(NewsletterRepository $newsletterRepo,Request $request): Response
    {
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class,['label' => 'Email :','attr'=>[
                'placeholder'=>'Votre email' ]   
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $customer= $form->get('email')->getData();
            $unsubscribe = $newsletterRepo->findOneByEmail($customer);
            if($unsubscribe==null)
            {
                $compare = null;
            }else{
                $compare =$unsubscribe->getEmail();
            }
            if($customer == $compare)
            {
                $unsubscribe->setUnsubscribe(true);
                $manager=$this->getDoctrine()->getManager();;
                $manager->persist($unsubscribe); 
                $manager->flush();
                return $this-> redirectToRoute('home_page');
            }else{
                $form->get('email')->addError(new FormError("cette adresse mail n'existe pas"));
            }
        }
        return $this->render('newsletter/unsubscribe.html.twig', [
            'form'=> $form->createView(),
        ]);
    }
}
