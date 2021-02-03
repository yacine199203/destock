<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Commande;
use App\Form\CommandeSearchType;
use App\Repository\PriceRepository;
use App\Form\CommandeNameSearchType;
use App\Repository\ProductRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommandeController extends AbstractController
{
    function int2str($a)
{
$convert = explode('.',$a);
$centimes='zero Centimes';
$zero= '';
if (isset($convert[0]) && $convert[0]=='0'){
    $zero= 'zero';
}
if (isset($convert[1]) && $convert[1]!='00')
{
    $centimes=' Centimes';
}
if (isset($convert[1]) && $convert[1]!=''){
    return $this->int2str($convert[0]).$zero.' Dinars'.' et '.$this->int2str($convert[1]).$centimes;
}
if ($a<0) return 'moins '.$this->int2str(-$a);
if ($a<17){
switch ($a){
case 0: return '';
case 1: return 'un';
case 2: return 'deux';
case 3: return 'trois';
case 4: return 'quatre';
case 5: return 'cinq';
case 6: return 'six';
case 7: return 'sept';
case 8: return 'huit';
case 9: return 'neuf';
case 10: return 'dix';
case 11: return 'onze';
case 12: return 'douze';
case 13: return 'treize';
case 14: return 'quatorze';
case 15: return 'quinze';
case 16: return 'seize';
}
} else if ($a<20){
return 'dix-'.$this->int2str($a-10);
} else if ($a<100){
if ($a%10==0){
switch ($a){
case 20: return 'vingt';
case 30: return 'trente';
case 40: return 'quarante';
case 50: return 'cinquante';
case 60: return 'soixante';
case 70: return 'soixante-dix';
case 80: return 'quatre-vingt';
case 90: return 'quatre-vingt-dix';
}
} elseif (substr($a, -1)==1){
if( ((int)($a/10)*10)<70 ){
return $this->int2str((int)($a/10)*10).'-et-un';
} elseif ($a==71) {
return 'soixante-et-onze';
} elseif ($a==81) {
return 'quatre-vingt-un';
} elseif ($a==91) {
return 'quatre-vingt-onze';
}
} elseif ($a<70){
return $this->int2str($a-$a%10).'-'.$this->int2str($a%10);
} elseif ($a<80){
return $this->int2str(60).'-'.$this->int2str($a%20);
} else{
return $this->int2str(80).'-'.$this->int2str($a%20);
}
} else if ($a==100){
return 'cent';
} else if ($a<200){
return $this->int2str(100).' '.$this->int2str($a%100);
} else if ($a<1000){
return $this->int2str((int)($a/100)).' '.'cents '.$this->int2str($a%100);
} else if ($a==1000){
return 'mille';
} else if ($a<2000){
return $this->int2str(1000).' '.$this->int2str($a%1000).' ';
} else if ($a<1000000){
return $this->int2str((int)($a/1000)).' '.$this->int2str(1000).' '.$this->int2str($a%1000);
}
else if ($a==1000000){
return 'millions';
}
else if ($a<2000000){
return 'un million ' . $this->int2str($a % 1000000) . ' ';
}
else if ($a<1000000000){
return $this->int2str((int)($a/1000000)).' '.$this->int2str(1000000).' '.$this->int2str($a%1000000);
}
}
    /**
     * @Route("/commande", name="commande")
     */
    public function index(CommandeRepository $commandeRepo,Request $request): Response
    {
        $commandes= $commandeRepo->findBy(array(), array('ref' => 'DESC'));
        $form = $this->createForm(CommandeSearchType::class);
        $form-> handleRequest($request);
        $formName = $this->createForm(CommandeNameSearchType::class);
        $formName-> handleRequest($request);

        $manager=$this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid())
        {
            $ref = $form->get('word')->getData();
            $commandes = $manager->createQuery('SELECT c FROM App\Entity\Commande c  WHERE c.ref LIKE \'%'.$ref.'%\''.' ORDER BY c.ref DESC')->getResult();
        }

        if($formName->isSubmitted() && $formName->isValid())
        {
            $name = $formName->get('word')->getData();
            $commandes = $manager->createQuery('SELECT c FROM App\Entity\Commande c  WHERE c.fullName LIKE \'%'.$name.'%\''.' ORDER BY c.ref DESC')->getResult();
        }
     
        return $this->render('commande/index.html.twig', [
            'commandes'=>$commandes,
            'form'=> $form->createView(),
            'formName'=> $formName->createView(),
            
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
        $commande->setFullName($this->getUser()->getLastName().' '.$this->getUser()->getFirstName());
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
            'chiffreEnLettre'=>$this->int2str(number_format($total, 2, '.', ''))
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
                "La commande <strong>".$removeCommande->getRef()."</strong> a bien été supprimée "
            );
        }
        return $this-> redirectToRoute('commande');         
        
    }
}
