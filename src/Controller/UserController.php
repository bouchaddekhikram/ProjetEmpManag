<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.php.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    #[Route('/employee', name: 'app_user_index_employee', methods: ['GET'])]
    public function listEmployees(UserRepository $userRepository): Response
    {
        $employees = $userRepository->findByRole('ROLE_EMPLOYEE');

        return $this->render('user/index.php.twig', [
            'users' => $employees,
        ]);
    }

    #[Route('/project-manager', name: 'app_user_index_project_manager', methods: ['GET'])]
    public function listProjectManagers(UserRepository $userRepository): Response
    {
        $projectManagers = $userRepository->findByRole('ROLE_PROJECT_MANAGER');

        return $this->render('user/index.php.twig', [
            'users' => $projectManagers,
        ]);
    }
    #[Route('/admin', name: 'app_user_index_admin', methods: ['GET'])]
    public function listAdmins(UserRepository $userRepository): Response
    {
        $admins = $userRepository->findByRole('ROLE_ADMIN');

        return $this->render('user/index.php.twig', [
            'users' => $admins,
        ]);
    }
    #[Route('/PM+EMP', name: 'employee_PM', methods: ['GET'])]
    public function listEmployeesandpm(UserRepository $userRepository): Response
    {
        $employeesAndPMs = $userRepository->findByRole2(['ROLE_EMPLOYEE', 'ROLE_PROJECT_MANAGER']);

        return $this->render('user/index.php.twig', [
            'users' => $employeesAndPMs,
        ]);
    }


    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserPasswordHasherInterface $userPasswordHasher ,EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )

            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('employee_PM', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
