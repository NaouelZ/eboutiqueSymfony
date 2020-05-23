<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\CommandLine;
use App\Entity\CustomerAddress;
use Doctrine\ORM\EntityManager;
use App\Form\CustomerAddressType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="cart_index")
     */
    public function index(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);

        $panierWithData = [];

        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        foreach($panierWithData as $item){
            $totalItem = $item['product']->getPriceht() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('cart/index.html.twig', [
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/panier/addone/{id}", name="cart_add_one") 
     */
    public function addOne($id, SessionInterface $session) {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++; 
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/panier/removeone/{id}", name="cart_remove_one") 
     */
    public function removeOne($id, SessionInterface $session) {
        $panier = $session->get('panier', []);

        $panier[$id]--; 

        if(empty($panier[$id])){
            unset($panier[$id]);
        }
        
        $session->set('panier', $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/panier/add/{id}", name="cart_add") 
     */
    public function add($id, SessionInterface $session) {
        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            $panier[$id]++; 
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session) {
        $panier = $session->get('panier', []);
        if(!empty($panier[$id])){
            unset($panier[$id]);
        }
        $session->set('panier',$panier);

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @Route("/new", name="address_new", methods={"GET","POST"})
     */
    public function addressCustomer(Request $request)
    {
        $customer = new CustomerAddress();
        $form = $this->createForm(CustomerAddressType::class, $customer);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $customer->setUser($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            // return $this->redirectToRoute('cart_command');
            return $this->render('cart/order.html.twig');
        }

        return $this->render('cart/address.html.twig', [
            'customer' => $customer,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/command", name="cart_command")
    */
    public function command(SessionInterface $session, EntityManagerInterface $manager, ProductRepository $productRepository ){
        $panier = $session->get('panier', []);

        $order = new Order();

        
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 15; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $ID = $randomString;

        $order->setOrderNumber((string) $ID)
              ->setValid(1)
              ->setMadeAt(new \DateTime('now'));
        
        $manager->persist($order);
        $manager->flush();


        foreach($panier as $id => $quantity){
            $commandLine = new CommandLine();

            $commandLine->setQuantity($quantity)
                        ->setProductOrder($productRepository->find($id))
                        ->setOrderCommand($order);

            $manager->persist($commandLine);
            $order->addCommandLine($commandLine);
        }


        return $this->render('cart/order.html.twig');
    }

}
