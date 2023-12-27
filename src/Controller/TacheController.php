<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Form\TacheTypeProject;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method getDoctrine()
 */
#[Route('/tache')]
class TacheController extends AbstractController
{
    #[Route('/userTaches', name: 'app_tache_userTaches', methods: ['GET'])]
    public function userTache(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();

        // Retrieve only the tasks belonging to the current user
        $userTaches = $user->getTaches();

        return $this->render('tache/index.html.twig', [
            'taches' => $userTaches,
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
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_projectTaches', [ 'projectId' => $tache->getProjet()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/project_taches_edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * Function that enable manager to add new task in a project
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

            $entityManager->persist($tache);
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_projectTaches', [ 'projectId' => $tache->getProjet()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/project_new_task.html.twig', [
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

    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
    }
}
