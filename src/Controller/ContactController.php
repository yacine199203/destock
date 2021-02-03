<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
     /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request,\Swift_Mailer $mailer)
    {
        $cont = new Contact();
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && empty($form->get('description')->getData())) {
            $contact = $form->getData();
            $name = $form->get('name')->getData();
            $mail = $form->get('email')->getData();
            $object = $form->get('object')->getData();
            $msg = $form->get('message')->getData();
            if($name == null)
            {
                $form->get('name')->addError(new FormError("ce champ est vide"));
            }elseif($mail == null)
            {
                $form->get('email')->addError(new FormError("ce champ est vide"));
            }elseif($object == null)
            {
                $form->get('object')->addError(new FormError("ce champ est vide"));
            }elseif($msg == null)
            {
                $form->get('message')->addError(new FormError("ce champ est vide"));
            }else{
                $file= $form->get('file')->getData();
                if($file != null)
                {
                    $fileName= uniqid().'.'.$file->guessExtension();
                    $cont->setPdf($fileName);
                    $file->move($this->getParameter('upload_directory_contact'),$fileName);
                    $message = (new \Swift_Message('Nouveau contact'))
                    // On attribue l'expéditeur
                    ->setFrom($form->get('email')->getData())
                    // On attribue le destinataire
                    ->setTo('test.formation.tf@gmail.com')
                    // On crée le texte avec la vue
                    ->setBody(
                        $this->renderView(
                            'contact/mail.html.twig', compact('contact')
                        ),
                        'text/html'
                    )
                    
                    ->attach(\Swift_Attachment::fromPath($this->getParameter('upload_directory_contact').'/'.$fileName))
                ;
                }else{
                    $message = (new \Swift_Message('Nouveau contact'))
                    // On attribue l'expéditeur
                    ->setFrom($form->get('email')->getData())
                    // On attribue le destinataire
                    ->setTo('test.formation.tf@gmail.com')
                    // On crée le texte avec la vue
                    ->setBody(
                        $this->renderView(
                            'contact/mail.html.twig', compact('contact')
                        ),
                        'text/html'
                    )
                ;
                }
                
                
                $mailer->send($message);
    
                $message1 = (new \Swift_Message('Accusé de récéption'))
                // On attribue l'expéditeur
                ->setFrom('bouadjenek.yacin@gmail.com')
                // On attribue le destinataire
                ->setTo($form->get('email')->getData())
                // On crée le texte avec la vue
                ->setBody(
                    $this->renderView(
                        'contact/mailTo.html.twig', compact('contact')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message1);
    
                $this->addFlash(
                    'success',
                    "Votre message a été transmis, nous vous répondrons dans les meilleurs délais."
                );
                $cont->setObject($object);
                $cont->setName($name);
                $manager=$this->getDoctrine()->getManager();
                $manager->persist($cont); 
                $manager->flush();
                return $this-> redirectToRoute('home_page');
            }

           
        }
        return $this->render('contact/index.html.twig',[
            'contactForm' => $form->createView(),
            ]);
    }

    /**
     * permet de voir la liste des contact
     * @Route("/dashbord/contacts", name="showContact")
     * @IsGranted("ROLE_ADMIN")
     */
    public function showCategory(ContactRepository $contactRepo): Response
    {
        $contact = $contactRepo->findAll();
        return $this->render('contact/showContact.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * permet de supprimer un message
     * @Route("/dashbord/supprimer-message/{id} ", name="removeMessage")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeMessage($id,ContactRepository $contactRepo)
    {   
        $removeMsg = $contactRepo->findOneById($id);
        $file= $removeMsg->getPdf();
        if($file!= null){
            unlink('../public/contact-file/'.$file);
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeMsg); 
        $manager->flush();
            $this->addFlash(
                'success',
                "Le message de <strong> ".$removeMsg->getName()."</strong> a bien été supprimé "
            );
            return $this-> redirectToRoute('showContact');
    }

    /**
     * permet de supprimer tous les abonnés 
     * @Route("/dashbord/supprimer-tous-les-messages ", name="removeAllMessages")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeAllMessage(ContactRepository $contactRepo)
    {  
        $allMessages = $contactRepo->findAll();
        $manager=$this->getDoctrine()->getManager();
        foreach ($allMessages as $msg)
        {
            $file= $msg->getPdf();
            if($file!= null){
                unlink('../public/contact-file/'.$file);
            }
            $manager->remove($msg); 
        }
        $manager->flush();
            $this->addFlash(
                'success',
                 "tous les messages ont été supprimés"
            );
            return $this-> redirectToRoute('showContact');
    }
}
