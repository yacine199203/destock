<?php

namespace App\Controller;

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
    public function addCommande($product,$dim,$qty,$price,CommandeRepository $commandeRepo,Request $request): Response
    {
    
        $p=explode(',',$product);
        $d=explode(',',$dim);
        $q=explode(',',$qty);
        $p=explode(',',$price);


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
        $inCart= new Cart();
        $inCart->setProduct($p[$j]);
        $inCart->setQte($q[$j]);
        $commande->addCart($inCart);
        }
        $manager->persist($commande);
        $manager->flush();
        $session->set('cart',[]);
        return $this-> redirectToRoute('commande');
    }
}
