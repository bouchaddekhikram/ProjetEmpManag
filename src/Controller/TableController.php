<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    #[Route('/table', name: 'app_table')]
    public function index(): Response
    {
        return $this->render('table/index.html.twig', [
            'controller_name' => 'TableController',
        ]);
    }
}
