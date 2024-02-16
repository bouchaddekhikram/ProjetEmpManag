<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProjetRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route("/", name:'app_homepage')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }




    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(UserRepository $userRepository, ProjetRepository $projetRepository): Response
    {
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Completed');
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

    #[Route('/dashboardEMP/{userId}', name: 'app_dashboard_emp')]
    public function dashboard_Emp(UserRepository $userRepository, ProjetRepository $projetRepository , int $userId): Response
    {
        $user = $userRepository->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Completed');
        $countProjectWaiting = $projetRepository->countProjectsByStatus('Waiting');
        $countEmployees = $userRepository->countUsersByRole('ROLE_EMPLOYEE');
        $countProjectManagers = $userRepository->countUsersByRole('ROLE_PROJECT_MANAGER');

 //--------------------get projects -----------
        // Retrieve tasks belonging to the current user
        $userTaches = $user->getTaches();

        // Extract projects from the tasks
        $userProjects = [];
        $manager = null;
        foreach ($userTaches as $tache) {
            $project = $tache->getProjet();

            // Ensure the project is not null before adding to the list
            if ($project !== null) {
                $projectId = $project->getId();
                $manager = $project->getUser();
                // Check if the project ID is already in the list
                if (!isset($userProjects[$projectId])) {
                    $userProjects[$projectId] = $project;
                }
            }
        }

        // Convert the associative array to a simple indexed array
        $userProjects = array_values($userProjects);
        $completedProjects = 0;
        $waitingProjects =0;
        foreach ($userProjects as $c) {
            if($c->getStatus() == "Completed")
            $completedProjects = $completedProjects+1;
            else{
                $waitingProjects =$waitingProjects+1;
            }
        }
//------------------------------------------------------


        return $this->render('dashboard/dashboard_Emp.html.twig', [
            'user' => $user->getFirstname(),
            'count_project_Competed' => $completedProjects,
            'count_project_Waiting' => $waitingProjects,
            'count_employee' => $countEmployees,
            'count_project_manager' => $countProjectManagers,
            'projects' => $userProjects,
            'manager' => $manager !== null ? $manager->getFirstName() : null,

        ]);
    }
    #[Route('/dashboardPM', name: 'app_dashboard_pm')]
    public function dashboard_PM(UserRepository $userRepository, ProjetRepository $projetRepository): Response
    {
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Completed');
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
    #[Route('/dashboardAdmin', name: 'app_dashboard_admin')]
    public function dashboard_AD(UserRepository $userRepository, ProjetRepository $projetRepository): Response
    {
        $countProjectCompeted = $projetRepository->countProjectsByStatus('Completed');
        $countProjectWaiting = $projetRepository->countProjectsByStatus('Waiting');
        $countEmployees = $userRepository->countUsersByRole('ROLE_EMPLOYEE');
        $countProjectManagers = $userRepository->countUsersByRole('ROLE_PROJECT_MANAGER');

        return $this->render('dashboard/dashboard_Admin.html.twig', [
            'count_project_Competed' => $countProjectCompeted,
            'count_project_Waiting' => $countProjectWaiting,
            'count_employee' => $countEmployees,
            'count_project_manager' => $countProjectManagers,
        ]);
    }
}