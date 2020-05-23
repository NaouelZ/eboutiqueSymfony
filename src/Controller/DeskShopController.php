<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Media;
use App\Form\UserType;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DeskShopController extends Controller
{
    /**
     * @Route("/deskshop", name="desk_shop")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findAll();
        $repo2 = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo2->findAll();
        return $this->render('desk_shop/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('desk_shop/home.html.twig');
    }

    /**
     * @Route("/deskshop/show/{id}", name="deskshop_show")
    */
    public function show($id) {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $product = $repo->find($id);
        return $this->render('desk_shop/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/deskshop/{category}", name="deskshop_category")
    */
    public function category($category) {
        $repo = $this->getDoctrine()->getRepository(Product::class);
        $products = $repo->findBy(
            ['category' => $category],
        );
        $repo2 = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo2->findAll();
        return $this->render('desk_shop/index.html.twig', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    /**
     * @Route("/profile", name="deskshop_profile")
    */
    public function profileAction(Request $request) {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->find($this->getUser());
        
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cart_index');
        }

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
