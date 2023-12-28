<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
//    #[Route('/dashboard', name: 'app_dashboard')]
//    public function index(): Response
//    {
//        return $this->render('dashboard/index.html.twig', [
//            'controller_name' => 'DashboardController',
//        ]);
//    }
//}

#[Route('/dashboard', name: 'app_dashboard')]

public function dashboard(UserRepository $userRepository, ProjetRepository $projetRepository): Response
{
    $countProjectCompeted = $projetRepository->countProjectsByStatus('Competed');
    $countProjectWaiting = $projetRepository->countProjectsByStatus('Waiting');
    $countEmployees = $userRepository->countUsersByRole('ROLE_EMPLOYEE');
    $countProjectManagers = $userRepository->countUsersByRole('ROLE_PROJECT_MANAGER');

    return $this->render('dashboard/index.html.twig', [
        'count_project_Competed' => $countProjectCompeted,
        'count_project_Waiting' => $countProjectWaiting,
        'count_employee' => $countEmployees,
        'count_project_manager' => $countProjectManagers,
    ]);
}}