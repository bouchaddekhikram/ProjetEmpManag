<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Form\TacheTypeEdit;
use App\Form\TacheTypeEmp;
use App\Form\TacheTypeProject;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method getDoctrine()
 */
#[Route('/tache')]
class TacheController extends AbstractController
{

    
//    #[Route('/userTaches', name: 'app_tache_userTaches', methods: ['GET'])]
//    public function userTache(): Response
//    {
//        // Get the currently logged-in user
//        $user = $this->getUser();
//
//        // Retrieve only the tasks belonging to the current user
//        $userTaches = $user->getTaches();
//
//        return $this->render('tache/index.html.twig', [
//            'taches' => $userTaches,
//        ]);
//    }

    /**
     * Function returns the projects of an Employee
     */

    #[Route('/userProjects', name: 'app_emp_userProjects', methods: ['GET'])]
    public function userTache(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();

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


        return $this->render('projet/employee/employee_projects.html.twig', [
            'projects' => $userProjects,
            'manager' => $manager !== null ? $manager->getFirstName() : null,
        ]);
    }


    /**
     * Function that returns a project's tasks of the current user
     */
    #[Route('/{projectId}/taches', name: 'app_tache_projectTaches_User', methods: ['GET'])]
    public function projectTacheUser($projectId,Security $security, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the current user
        $user = $security->getUser();


        // Retrieve the project by ID
        $project = $entityManager->getRepository(Projet::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Retrieve tasks associated with the project and the current user
        $projectTaches = $project->getTaches();

//            ->filter(function ($tache) use ($user) {
//            // Assuming there's a method like `getUser` to retrieve the user associated with the task
//            return $tache->getUsers() === $user;
//        });

        if($projectTaches->isEmpty()){
            return $this->redirectToRoute('app_new_task',['projectId' => $project->getId()]);
        }


        return $this->render('tache/project_taches_emp.html.twig', [
            'taches' => $projectTaches,
            'projet'=>$project,
            'user' =>$user,
        ]);
    }





    /**
     * Function that returns a project's tasks
     */
    #[Route('/{projectId}/taches', name: 'app_tache_projectTaches', methods: ['GET'])]
    public function projectTache($projectId, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the project by ID
        $project = $entityManager->getRepository(Projet::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        // Retrieve tasks associated with the project
        $projectTaches = $project->getTaches();

        if($projectTaches->count() == 0){
            return $this->redirectToRoute('app_new_task',['projectId' => $project->getId()]);
        }


        return $this->render('tache/project_taches.html.twig', [
            'taches' => $projectTaches,
        ]);
    }

    /**
     * Function that enable to show a task details
     */

    #[Route('/{id}', name: 'app_project_tache_show', methods: ['GET'])]
    public function showTask(Tache $tache): Response
    {
        return $this->render('tache/project_taches_show.html.twig', [
            'tache' => $tache,
        ]);
    }
    /**
     * Function that enable the Manager to update his projects' tasks
     */
    #[Route('/{id}/edit/xx', name: 'app_tache_projectTachesedit', methods: ['GET', 'POST'])]
    public function projectEdit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheTypeProject::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // Call the method to update Projet status
            $this->updateProjetStatus($tache->getProjet(),$entityManager);

            return $this->redirectToRoute('app_tache_projectTaches', [ 'projectId' => $tache->getProjet()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/project_taches_edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }


    /**
     * Update the status of tasks to Pending
     */
    #[Route('/{id}/TaskPending/xx', name: 'app_tache_pending', methods: ['POST'])]
    public function taskPending(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        // Call the method to update the status of the task to "Pending"
        $this->updateTaskStatusToPending($tache, $entityManager);
        // Call the method to update Projet status
        $this->updateProjetStatus($tache->getProjet(),$entityManager);

        // Return a JSON response indicating success
        return new JsonResponse(['message' => 'Task status updated to Pending']);
    }

// Method to update the status of the task to "Pending"
    private function updateTaskStatusToPending(Tache $tache, EntityManagerInterface $entityManager): void
    {
        $tache->setStatus('Pending'); // Assuming 'Pending' is a valid status for your task entity
        $entityManager->persist($tache);
        $entityManager->flush();
    }


    /**
     * Update the status of tasks to Waiting
     */
    #[Route('/{id}/TaskWaiting/xx', name: 'app_tache_waiting', methods: ['POST'])]
    public function taskWaiting(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        // Call the method to update the status of the task to "Pending"
        $this->updateTaskStatusToWaiting($tache, $entityManager);
        // Call the method to update Projet status
        $this->updateProjetStatus($tache->getProjet(),$entityManager);

        // Return a JSON response indicating success
        return new JsonResponse(['message' => 'Task status updated to Pending']);
    }

// Method to update the status of the task to "Pending"
    private function updateTaskStatusToWaiting(Tache $tache, EntityManagerInterface $entityManager): void
    {
        $tache->setStatus('Waiting'); // Assuming 'Pending' is a valid status for your task entity
        $entityManager->persist($tache);
        $entityManager->flush();
    }




    /**
     * Update the status of tasks to Completed
     */
    #[Route('/{id}/TaskCompleted/xx', name: 'app_tache_completed', methods: ['POST'])]
    public function taskCompleted(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        // Call the method to update the status of the task to "Pending"
        $this->updateTaskStatusToCompleted($tache, $entityManager);
        // Call the method to update Projet status
        $this->updateProjetStatus($tache->getProjet(),$entityManager);

        // Return a JSON response indicating success
        return new JsonResponse(['message' => 'Task status updated to Pending']);
    }

// Method to update the status of the task to "Pending"
    private function updateTaskStatusToCompleted(Tache $tache, EntityManagerInterface $entityManager): void
    {
        $tache->setStatus('Completed'); // Assuming 'Pending' is a valid status for your task entity
        $entityManager->persist($tache);
        $entityManager->flush();
    }







    #[Route('/{id}/edit/emp', name: 'app_Taches_editEmp', methods: ['GET', 'POST'])]
    public function projectEditEmp(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheTypeEmp::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // Call the method to update Projet status
            $this->updateProjetStatus($tache->getProjet(),$entityManager);

            return $this->redirectToRoute('app_tache_projectTaches', [ 'projectId' => $tache->getProjet()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/project_taches_edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);

    }


    /**
     * Function to update the Projet status based on Tache statuses
     */
    private function updateProjetStatus(Projet $projet, EntityManagerInterface $entityManager): void
    {
        $taches = $projet->getTaches();
        $projetStatus = 'Pending'; // Set default status to 'Pending'

        $allCompleted = true; // Flag to track if all tasks are completed

        foreach ($taches as $tache) {
            $tacheStatus = $tache->getStatus();

            // Define your logic for updating the projet status based on tache status
            if ($tacheStatus === 'Waiting') {
                $projetStatus = 'Waiting';
                $allCompleted =false;
                break;
            } elseif ($tacheStatus !== 'Completed') {
                // If any task is not 'Completed', set the flag to false
                $allCompleted = false;
            }
        }

        // If all tasks are 'Completed', set projet status to 'Completed'
        if ($allCompleted) {
            $projetStatus = 'Completed';
        }

        $projet->setStatus($projetStatus);
         $entityManager->flush();
    }

    /**
     * Function that enable Manager to add new task in a project
     */
    #[Route('/new/{projectId}/xxx', name: 'app_new_task_manager', methods: ['GET', 'POST'])]
    public function newTaskM(Request $request, EntityManagerInterface $entityManager, int $projectId): Response
    {
        $tache = new Tache();
        // Get the project based on the provided projectId
        $project = $entityManager->getRepository(Projet::class)->find($projectId);

        // Set the project for the task
        $tache->setProjet($project);

        $form = $this->createForm(TacheTypeProject::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Check if the task dates are within the project dates
            $taskStartDate = $tache->getDateDebut();
            $taskFinishDate = $tache->getDataFin();

            if (
                $taskStartDate < $project->getDateDebut() ||
                $taskFinishDate > $project->getDataFin()
            ) {
                // Handle the case when task dates are outside the project dates
                $this->addFlash('error', 'Task dates must be within the project dates.');
            } else {
                $entityManager->persist($tache);
                $entityManager->flush();
                // Call the method to update Projet status
                $this->updateProjetStatus($tache->getProjet(),$entityManager);
                return $this->render('projet/manager/managerP_show.html.twig', [
                    'projet' => $project,
                    'taches' =>$project->getTaches()
                ]);


            }

        }

        return $this->render('tache/project_new_task.html.twig', [
            'projet' => $tache->getProjet(),
            'tache' => $tache,
            'form' => $form,
        ]);
    }





    /**
     * Function that enable Admin to add new task in a project
     */
    #[Route('/new/{projectId}/xxx', name: 'app_new_task', methods: ['GET', 'POST'])]
    public function newTask(Request $request, EntityManagerInterface $entityManager, int $projectId): Response
    {
        $tache = new Tache();
        // Get the project based on the provided projectId
        $project = $entityManager->getRepository(Projet::class)->find($projectId);

        // Set the project for the task
        $tache->setProjet($project);

        $form = $this->createForm(TacheTypeProject::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Check if the task dates are within the project dates
            $taskStartDate = $tache->getDateDebut();
            $taskFinishDate = $tache->getDataFin();

            if (
                $taskStartDate < $project->getDateDebut() ||
                $taskFinishDate > $project->getDataFin()
            ) {
                // Handle the case when task dates are outside the project dates
                $this->addFlash('error', 'Task dates must be within the project dates.');
            } else {
                $entityManager->persist($tache);
                $entityManager->flush();
                // Call the method to update Projet status
                $this->updateProjetStatus($tache->getProjet(),$entityManager);
                return $this->render('projet/admin/admin_show.html.twig', [
                    'projet' => $project,
                    'taches' =>$project->getTaches()
                ]);


            }

        }

        return $this->render('tache/project_new_task.html.twig', [
            'projet' => $tache->getProjet(),
            'tache' => $tache,
            'form' => $form,
        ]);
    }
















    #[Route('/', name: 'app_tache_index', methods: ['GET'])]
    public function index(TacheRepository $tacheRepository): Response
    {
        return $this->render('tache/index.html.twig', [
            'taches' => $tacheRepository->findAll(),
        ]);
    }

    #[Route('/new/xx', name: 'app_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tache = new Tache();
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/xx', name: 'app_tache_show', methods: ['GET'])]
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * Function that enable the admin to edit a task
     */

    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheTypeEdit::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->render('projet/admin/admin_show.html.twig', [
                'projet' => $tache->getProjet(),
                'taches' =>$tache->getProjet()->getTaches()
            ]);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
            'projet' => $tache->getProjet(),
        ]);
    }


    /**
     * Function that enable the admin to edit a task
     */

    #[Route('/{id}/editM', name: 'app_tache_edit_manager', methods: ['GET', 'POST'])]
    public function editM(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheTypeEdit::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->render('projet/manager/managerP_show.html.twig', [
                'projet' => $tache->getProjet(),
                'taches' =>$tache->getProjet()->getTaches()
            ]);
        }

        return $this->render('tache/Medit.html.twig', [
            'tache' => $tache,
            'form' => $form,
            'projet' => $tache->getProjet(),
        ]);
    }


    #[Route('/{id}', name: 'app_tache_Mdelete', methods: ['POST'])]
    public function deleteM(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
            $idP = $tache->getProjet()->getId();

            $entityManager->remove($tache);
            $entityManager->flush();


            $this->updateProjetStatus($tache->getProjet(),$entityManager);

        }

        return $this->render('projet/manager/managerP_show.html.twig', [
            'projet' => $tache->getProjet(),
            'taches' =>$tache->getProjet()->getTaches()
        ]);
    }








    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
           $idP = $tache->getProjet()->getId();

            $entityManager->remove($tache);
            $entityManager->flush();


            $this->updateProjetStatus($tache->getProjet(),$entityManager);

        }

        return $this->render('projet/admin/admin_show.html.twig', [
            'projet' => $tache->getProjet(),
            'taches' =>$tache->getProjet()->getTaches()
        ]);
    }
}
