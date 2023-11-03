<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig', []);
    }

    #[Route('/service', name: 'service')]
    public function home(): Response
    {
        return $this->render('page/service.html.twig', []);
    }

    #[Route('/pet', name: 'pet')]
    public function pet(): Response
    {
        return $this->render('page/pet.html.twig', []);
    }

    #[Route('/clinic', name: 'clinic')]
    public function clinic(): Response
    {
        return $this->render('page/clinic.html.twig', []);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig', []);
    }

    #[Route('/buy', name: 'buy')]
    public function buy(): Response
    {
        return $this->render('page/buy.html.twig', []);
    }
}
