<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * @Route("/{_locale}", requirements={ "_locale": "en|es" }, defaults={"_locale": "en"} )
 */
class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, EntityManagerInterface $manager){
        $product = new Product();
        $form = $this->createFormBuilder($product)
            ->add('title')
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('home');
        }


        return $this->render('home/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $manager){
        $form = $this->createFormBuilder($product)
            ->add('title')
            ->add('description', TextareaType::class)
            ->getForm()
        ;

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, EntityManagerInterface $manager, Product $product){
        if(!$this->isCsrfTokenValid('delete_product'.$product->getId(), $request->request->get('csrf_token'))){
            return new InvalidCsrfTokenException('CSRF Token invalid');
        }

        $manager->remove($product);
        $manager->flush();

        return $this->redirectToRoute('home');
    }
}
