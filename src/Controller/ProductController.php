<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\ProductImages;
use App\Repository\ProductRepository;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * permet de voir les produits
     * @Route("/produits", name="product")
     */
    public function index(ProductRepository $productRepo): Response
    {
        $products=$productRepo->findAll();
        return $this->render('product/index.html.twig', [
            'products'=>$productRepo,
        ]);
    }

    /**
    * permet d'ajouter un produit
     * @Route("/dashbord/ajouter-produit", name="addProduct")
     * @return Response
     */
    public function addProduct(Request $request)
    {
        $addProduct = new Product();
        $addProdForm = $this->createForm(ProductType::class,$addProduct);
        $addProdForm-> handleRequest($request);
        if($addProdForm->isSubmitted() && $addProdForm->isValid() && empty($addProdForm->get('description')->getData()))
        { 
            $filePng= $addProdForm->get('png')->getData();
            $filePdf= $addProdForm->get('pdf')->getData();
            $fileImage= $addProdForm->get('image')->getData();
            if($filePng == null)
            {
                $addProdForm->get('png')->addError(new FormError("Ce champ est vide"));
            }
            elseif($filePdf == null)
            {
                $addProdForm->get('pdf')->addError(new FormError("Ce champ est vide"));
            }
            elseif($fileImage == null)
            {
                $addProdForm->get('image')->addError(new FormError("Ce champ est vide"));
            } else
            {
                
                if($filePng->guessExtension()!='png')
                {
                    $addProdForm->get('png')->addError(new FormError("votre image doit être en format png"));
                }
                elseif($filePdf->guessExtension()!='pdf')
                {
                    $addProdForm->get('pdf')->addError(new FormError("votre fichier doit être en format pdf"));
                }
                foreach($fileImage as $img)
                {
                    if($img->guessExtension()!='png')
                    {
                        $addProdForm->get('image')->addError(new FormError("vos images doivent être en format png"));
                    }
                }

                $fileNamePng=  uniqid().'.'.$filePng->guessExtension();
                $fileNamePdf=  uniqid().'.'.$filePdf->guessExtension();

                $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                $addProduct->setPng($fileNamePng);
                $addProduct->setPdf($fileNamePdf);

                $manager=$this->getDoctrine()->getManager();
                foreach($fileImage as $image){
                    // On génère un nouveau nom de fichier
                    $fileNameImage = md5(uniqid()).'.'.$image->guessExtension();
                    
                    // On copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('upload_directory_png'),
                        $fileNameImage
                    );
                    
                    // On crée l'image dans la base de données
                    $img = new ProductImages();
                    $img->setImage($fileNameImage);
                    $addProduct->addProductImage($img);
                }

                foreach ($addProduct->getCharacteristics() as $chara)
                {
                    $chara->setProduct($addProduct);
                    $manager->persist($chara);
                }

                $manager->persist($addProduct); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le produit <strong>".$addProduct->getProductName()."</strong> a bien été ajouté "
                );
                return $this-> redirectToRoute('product'); 
            }
        }
        return $this->render('product/addProduct.html.twig', [
            'addProdForm'=> $addProdForm->createView(),
            
        ]);
    }
}
