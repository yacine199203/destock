<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\JobProduct;
use App\Entity\ProductImages;
use App\Form\EditProductType;
use App\Repository\JobRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Form\FormError;
use App\Repository\JobProductRepository;
use App\Repository\ProductImagesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     * @IsGranted("ROLE_ADMIN")
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
                }else
                {
                    foreach($fileImage as $img)
                    {
                        if($img->guessExtension()!='png')
                        {
                            $addProdForm->get('image')->addError(new FormError("vos images doivent être en format png"));
                            $this->addFlash(
                                'danger',
                                "Le produit n'a pas été ajouté, la galerie photo contient un fichier qui n'est pas au format png"
                            );
                            return $this-> redirectToRoute('product');

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
    
    
                    $jobProdData = $addProdForm->get('jobProducts')->getData();
                    foreach($jobProdData as $jp){
                        $r= new JobProduct();
                        $r->setJob($jp);
                        $addProduct->addJobProduct($r);
                    }
    
                    foreach ($addProduct->getPrices() as $price)
                    {
                        $price->setProduct($addProduct);
                        $manager->persist($price);
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
        }
        return $this->render('product/addProduct.html.twig', [
            'addProdForm'=> $addProdForm->createView(),
            
        ]);
    }

    /**
    * permet de modifier un produit
     * @Route("/dashbord/modifier-produit/{slug}", name="editProduct")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function editProduct($slug,ProductRepository $productRepo,Request $request)
    {
        $editProduct=$productRepo->findOneBySlug($slug);
        $editProdForm = $this->createForm(EditProductType::class,$editProduct);
        $editProdForm-> handleRequest($request);
        if($editProdForm->isSubmitted() && $editProdForm->isValid() && empty($editProdForm->get('description')->getData()))
        { 
            $filePng= $editProdForm->get('png')->getData();
            $filePdf= $editProdForm->get('pdf')->getData();
            $fileImage= $editProdForm->get('image')->getData();

            if($filePng != null)
            {
                if($filePng->guessExtension()!='png')
                {
                    $editProdForm->get('png')->addError(new FormError("Le format de ce fichier doit être en png"));
                }else
                {
                    unlink('../public/images/'.$editProduct->getPng());
                    $fileNamePng=  uniqid().'.'.$filePng->guessExtension();
                    $filePng->move($this->getParameter('upload_directory_png'),$fileNamePng);
                    $editProduct->setPng($fileNamePng);
                }    
            }

            if($filePdf != null)
            {
                if($filePdf->guessExtension()!='pdf')
                {
                    $editProdForm->get('pdf')->addError(new FormError("Le format de ce fichier doit être en PDF"));   
                }else
                {
                    unlink('../public/fiches-technique/'.$editProduct->getPdf());
                    $fileNamePdf=  uniqid().'.'.$filePdf->guessExtension();
                    $filePdf->move($this->getParameter('upload_directory_pdf'),$fileNamePdf);
                    $editProduct->setPdf($fileNamePdf);
                }
            }

            if($fileImage != null)
            {
                foreach($fileImage as $image)
                {
                    if($image->guessExtension()!='png')
                        {
                            $editProdForm->get('image')->addError(new FormError("vos images doivent être en format png"));
                            $this->addFlash(
                                'danger',
                                "Le produit n'a pas été modifié, la galerie photo contient un fichier qui n'est pas au format png"
                            );
                            return $this-> redirectToRoute('product');

                        }
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
                    $editProduct->addProductImage($img);
                }
            }

            $manager=$this->getDoctrine()->getManager();

            $r= new JobProduct();
            $t1=$editProdForm->get('jobProducts')->getData();
            if($t1==null)
            {
                $editProdForm->get('jobProducts')->addError(new FormError("ajoutez au moins un métier"));
            }else{
                $error= false;
                foreach($editProduct->getJobProducts()as $ejp)
                {
                    $t2=$ejp->getJob();        
                    if($t1 == $t2)
                    {
                        $error= true;
                        break;
                    }else
                    {
                        $error=false ;
                    }
                }
                if( $error==true){
                    $editProdForm->get('jobProducts')->addError(new FormError("ce produit appartient déja à ce métier"));
                }else
                {
                    $r->setJob($editProdForm->get('jobProducts')->getData());
                    $editProduct->addJobProduct($r);
                }

                foreach ($editProduct->getPrices() as $price)
                {
                    $price->setProduct($editProduct);
                    $manager->persist($price);
                }

                foreach ($editProduct->getCharacteristics() as $chara)
                {
                    $chara->setProduct($editProduct);
                    $manager->persist($chara);
                }

                $manager->persist($editProduct); 
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le produit <strong>".$editProduct->getProductName()."</strong> a bien été modifié "
                );
                return $this-> redirectToRoute('product');
                }
            
        }
        return $this->render('product/editProduct.html.twig', [
            'editProdForm'=> $editProdForm->createView(),
            'editProduct'=> $editProduct,
        ]);
    }

    /**
    * @Route("/supprimer-images-produit/{id}", name="removePi")
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteImage( $id,ProductImagesRepository $piRepo)
    {
        $removepi = $piRepo->findOneById($id);
        $file= $removepi->getImage();
        if($removepi->getImage() != null){
            unlink('../public/images/'.$file);
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removepi); 
        $manager->flush();
        return $this->json(['code'=> 200, 'message'=>'image supprimée'],200);
    }

    /**
    * @Route("/supprimer-metiers-produit/{id}", name="removePj")
    * @IsGranted("ROLE_ADMIN")
    */
    public function deleteJob( $id,JobProductRepository $pjRepo)
    {
        $removepj = $pjRepo->findOneById($id);
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removepj); 
        $manager->flush();
        return $this->json(['code'=> 200, 'message'=>'métier supprimé'],200);
    }

    /**
     * permet de supprimer un produit
     * @Route("/dashbord/supprimer-produit/{id} ", name="removeProduct")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function removeProduct($id,ProductRepository $productRepo)
    {   
        $removeProduct = $productRepo->findOneById($id);
        $filePng= $removeProduct->getPng();
        $fileImage= $removeProduct->getProductImages();
        $filePdf= $removeProduct->getPdf();
        if($removeProduct->getPng() != null){
            unlink('../public/images/'.$removeProduct->getPng());
            
        }
        if($removeProduct->getPdf() != null){
            unlink('../public/fiches-technique/'.$removeProduct->getPdf());
        }

        foreach($removeProduct->getProductImages() as $rp)
        {
            if($rp != null){
                unlink('../public/images/'.$rp->getImage());
            }
        }
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($removeProduct); 
        $manager->flush();
        $this->addFlash(
            'success',
            "Le produit <strong>".$removeProduct->getProductName()."</strong> a bien été supprimé "
        );
        return $this-> redirectToRoute('product');
    }
}
