<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Image;
use App\Form\CategoryFormType;
use App\Form\ImageFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin/image/add', name: 'add_images')]
public function images(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
{
    $repositorio = $doctrine->getRepository(Image::class);

    $images = $repositorio->findAll();

    $image = new Image();
    $form = $this->createForm(ImageFormType::class, $image);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('file')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    
            // Move the file to the directory where images are stored
            try {
    
                $file->move(
                    $this->getParameter('images_directory'), $newFilename
                );
                
    
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
    
            // updates the 'file$filename' property to store the PDF file name
            // instead of its contents
            $image->setFile($newFilename);
        }
        $image = $form->getData();   
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($image);
        $entityManager->flush();
        return $this->redirectToRoute('add_images', []);
    }
    return $this->render('admin/images.html.twig', array(
                'form' => $form->createView(),
                'image' => $image,
                'images' => $images
            ));
    }

    #[Route('/admin/images/delete/{id}', name: 'delete_image')]
    public function deleteImage(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Image::class);
        $image = $repositorio->find($id);
        if ($image){
                $entityManager->remove($image);
                $entityManager->flush();
                return $this->redirectToRoute('add_images', []);
        }else
            return $this->render('images.html.twig', ['image' => null] ,);
    }

    #[Route('/admin/categories', name: 'app_categories')]
    public function categories(ManagerRegistry $doctrine, Request $request): Response
    {
    $repositorio = $doctrine->getRepository(Category::class);

    $categories = $repositorio->findAll();

    $category = new Category();
    $form = $this->createForm(CategoryFormType::class, $category);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $category = $form->getData();    
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($category);
        $entityManager->flush();
        return $this->redirectToRoute('app_categories', []);
    }
    return $this->render('admin/categories.html.twig', array(
        'form' => $form->createView(),
        'categories' => $categories   
    ));
}

    #[Route('/admin/categories/delete/{id}', name: 'delete_category')]
    public function deleteCategory(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Category::class);
        $category = $repositorio->find($id);
        if ($category){
                $entityManager->remove($category);
                $entityManager->flush();
                return $this->redirectToRoute('app_categories', []);
        }else
            return $this->render('admin/categories.html.twig', ['category' => null]);
    }


    public function adminDashboard(): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');

    // or add an optional message - seen by developers
    $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
    
    new Response("Sí que puedes entrar");
}


}