<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\Tache;
use App\Form\ProjetType;
use App\Form\ProjetTypeEdit;
use App\Form\ProjetTypeEditAdmin;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projet')]
class ProjetController extends AbstractController
{
    /**
     * userProjet() => Get the Project of the current user
     */
    #[Route('/userprojets', name: 'app_projet_userProjets', methods: ['GET'])]
    public function userProjet(): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();

        // Retrieve only the tasks belonging to the current user
        $userProjets = $user->getProjets();

        return $this->render('projet/manger/manager_projects.html.twig', [
            'projets' => $userProjets,
        ]);
    }










    #[Route('/', name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }
    #[IsGranted("ROLE_ADMIN")]
    #[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projet);
            $entityManager->flush();

//            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
            return $this->render('projet/admin/admin_show.html.twig', [
                'projet' => $projet,
                'taches' =>$projet->getTaches()
            ]);
        }

        return $this->render('projet/admin/new.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    /**
     * Function to Show Manager Projects without Delete button
     */

    #[Route('/{id}/xx', name: 'app_manager_projet_show', methods: ['GET'])]
    public function managerShow(Projet $projet): Response
    {
        return $this->render('projet/manager/manager_show.html.twig', [
            'projet' => $projet,
        ]);
    }

    #[Route('/{id}/showEp', name: 'app_admin_projet_show', methods: ['GET'])]
    public function adminShow(Projet $projet): Response
    {
        return $this->render('projet/admin/admin_show.html.twig', [
            'projet' => $projet,
            'taches' =>$projet->getTaches()
        ]);
    }

    #[Route('/{id}/showManager', name: 'app_manager_projet_show', methods: ['GET'])]
    public function ManegerShow(Projet $projet): Response
    {
        return $this->render('projet/manager/managerP_show.html.twig', [
            'projet' => $projet,
            'taches' =>$projet->getTaches()
        ]);
    }





    /**
     * Function that enable the chef to update only his own projects
     */
    #[Route('/{id}/edit/manager', name: 'app_projet_manager_edit', methods: ['GET', 'POST'])]
    public function managerEdit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetTypeEdit::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_userProjets', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/manager/manager_updates.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edit/admin', name: 'app_projet_admin_edit', methods: ['GET', 'POST'])]
    public function AdminEdit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetTypeEditAdmin::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->render('projet/admin/admin_show.html.twig', [
                'projet' => $projet,
                'taches' =>$projet->getTaches()
            ]);
        }

        return $this->render('projet/admin/admin_updates.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);
    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/{id}', name: 'app_projet_delete', methods: ['POST'])]
    public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
