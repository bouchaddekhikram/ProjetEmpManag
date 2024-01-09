<?php

namespace App\Controller;

use App\Repository\ProjetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route("/", name:'app_homepage')]
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
    }

    #[Route('/dashboardEMP', name: 'app_dashboard_emp')]
    public function dashboard_Emp(UserRepository $userRepository, ProjetRepository $projetRepository): Response
    {
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Competed');
        $countProjectWaiting = $projetRepository->countProjectsByStatus('Waiting');
        $countEmployees = $userRepository->countUsersByRole('ROLE_EMPLOYEE');
        $countProjectManagers = $userRepository->countUsersByRole('ROLE_PROJECT_MANAGER');

        return $this->render('dashboard/dashboard_Emp.html.twig', [
            'count_project_Competed' => $countProjectCompeted,
            'count_project_Waiting' => $countProjectWaiting,
            'count_employee' => $countEmployees,
            'count_project_manager' => $countProjectManagers,
        ]);
    }
    #[Route('/dashboardPM', name: 'app_dashboard_pm')]
    public function dashboard_PM(UserRepository $userRepository, ProjetRepository $projetRepository): Response
    {
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Competed');
        $countProjectWaiting = $projetRepository->countProjectsByStatus('Waiting');
        $countEmployees = $userRepository->countUsersByRole('ROLE_EMPLOYEE');
        $countProjectManagers = $userRepository->countUsersByRole('ROLE_PROJECT_MANAGER');

        return $this->render('dashboard/dashboard_PM.html.twig', [
            'count_project_Competed' => $countProjectCompeted,
            'count_project_Waiting' => $countProjectWaiting,
            'count_employee' => $countEmployees,
            'count_project_manager' => $countProjectManagers,
        ]);
    }
}