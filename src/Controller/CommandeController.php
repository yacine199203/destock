<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(CommandeRepository $commandeRepo,Request $request): Response
    {
        $commandes= $commandeRepo->findBy(array(), array('ref' => 'DESC'));
        $session = $request->getSession();

        return $this->render('commande/index.html.twig', [
            'commandes'=>$commandes,
            'session'=>$session,
        ]);
    }

    /**
     * @Route("/commande/panier", name="cart")
     */
    public function cart(Request $request,ProductRepository $productRepo): Response
    {
        $session = $request->getSession();       
        $cart = $session->get('cart',[]);
        $dimension = $session->get('dimension',[]);
        $prices = $session->get('prices',[]);
        $inCart=[];
        $total = 0;
        foreach($cart as $test=>$qty)
        {
            $inCart[]=[
                'product'=>$productRepo->find(str_replace($dimension[$test].$prices[$test], "", $test)),
                'dim'=> $dimension[$test],
                'qty'=>$qty,
                'price'=> $prices[$test],
                'montant'=> floatval($prices[$test]) * $qty,
            ];
        }
      
        return $this->render('commande/cart.html.twig', [
            'items' => $inCart,
        ]);
    }

    /**
     * @Route("/commande/panier/ajouter/{id}/{dim}/{price}", name="addCart")
     */
    public function addCart($id,$dim,$price,Request $request): Response
    {
        $session = $request->getSession();
        $cart = $session->get('cart',[]);
        $dimension = $session->get('dimension',[]);
        $prices = $session->get('prices',[]);
        $test=$id.$dim.$price;
        if(!empty($cart[$test]) && !empty($dimension[$test]))
        {
            $cart[$test]++;

        }else
        {
            $cart[$test]=1;
            $dimension[$test]=$dim;
            $prices[$test]=$price;
        }
        $session->set('cart',$cart);
        $session->set('dimension',$dimension);
        $session->set('prices',$prices);
        return $this->json(['code'=> 200, 'message'=>'produit ajoute au panier','data'=>count($cart)],200); 
    }

    /**
     * @Route("/commande/panier/supprimer/{id}/{dim}/{price}", name="removeCart")
     */
    public function removeCart($id,$dim,$price,Request $request): Response
    {
        
        $session = $request->getSession();
        $cart = $session->get('cart',[]);
        $dimension = $session->get('cart',[]);
        $prices = $session->get('prices',[]);
        if(!empty($cart[$id.$dim.$price]))
        {
            unset($cart[$id.$dim.$price]);
            unset($dimension[$id.$dim.$price]);
            unset($prices[$id.$dim.$price]);
        }
        $session->set('cart',$cart);
        
        return $this-> redirectToRoute('cart'); 
    }

    /**
     * @Route("/commande/ajouter/{product}/{dim}/{qty}/{price}", name="addCommande")
     */
    public function addCommande($product,$dim,$qty,$price,CommandeRepository $commandeRepo,ProductRepository $prodRepo,Request $request,PriceRepository $priceRepo): Response
    {
    
        $p=explode(',',$product);
        $d=explode(',',$dim);
        $q=explode(',',$qty);
        $pr=explode(',',$price);

        $session = $request->getSession();
        $commandes = $commandeRepo->findBy(array(), array('id' => 'DESC'));
        $commande= new Commande();
        $manager=$this->getDoctrine()->getManager();
        if(empty($commandes)){
            $i=0;
        }elseif(($commandes[0]->getCreatedAt()->format('m')<date('m') && $commandes[0]->getCreatedAt()->format('Y') == date('Y')) || ($commandes[0]->getCreatedAt()->format('m')>date('m') && $commandes[0]->getCreatedAt()->format('Y') < date('Y'))){
            $i=0;
        }else{
            $i=$commandes[0]->getCounter();
        }
        $ref= date('Y').date('m').str_pad(($i+1), 4, '0', STR_PAD_LEFT);
        $commande->setRef($ref);
        $commande->setUser($this->getUser());
        $commande->setCounter($i+1);
        $commande->setValid(false);
        for($j=0;$j<count($p);$j++)
        {
            $pid= $prodRepo->findOneByProductName($p[$j]);
            
            $product= $manager->createQuery('SELECT p.id FROM App\Entity\Price p WHERE p.product ='.$pid->getId().'AND p.dimension ='.$d[$j])->getResult();
            $prid= $priceRepo->findOneById($product[0]);
            
            $inCart= new Cart();
            $inCart->setProduct($product);
            $prid->addCart($inCart);
            $inCart->setQty($q[$j]);
            $inCart->setPrice($pr[$j]);
            $commande->addCart($inCart);
        }
        $manager->persist($commande);
        $manager->flush();
        $session->set('cart',[]);
        return $this-> redirectToRoute('commande');
    }

    /**
     * @Route("/commande/valider/{id}", name="validateCommande")
     */
    public function validateCommande($id,CommandeRepository $commandeRepo,Request $request): Response
    {
        $valid= $commandeRepo->findOneById($id);
        $valid->setValid(true);
        $manager=$this->getDoctrine()->getManager();
        $manager->persist($valid);
        $manager->flush();
        return $this-> redirectToRoute('commande');
    }

    /**
     * permet de voir une commande
     * @Route("/commande/ma-commande/{id} ", name="showCommande")
     * @return Response
     */
    public function showeCommande($id,CommandeRepository $commandeRepo)
    {   

        $showCommande = $commandeRepo->findOneById($id);
        $carts = $showCommande->getCarts();
        $total=0;
        foreach ($carts as $cart)
        {
            $total = $total + $cart->getQty() * $cart->getPrice();
        }
    
        return $this->render('commande/showCommande.html.twig', [
            'showCommande' => $showCommande,
            'total' => $total,
        ]);       
        
    }

    /**
     * permet de supprimer une commande
     * @Route("/commande/supprimer/{id} ", name="removeCommande")
     * @return Response
     */
    public function removeCommande($id,CommandeRepository $commandeRepo)
    {   

        $removeCommande = $commandeRepo->findOneById($id);
        if($removeCommande->getValid()== false)
        {
            $manager=$this->getDoctrine()->getManager();
            $manager->remove($removeCommande); 
            $manager->flush();
            $this->addFlash(
                'success',
                "La commande ".$removeCommande->getRef()." a bien été supprimée "
            );
        }
        return $this-> redirectToRoute('commande');         
        
    }
}
