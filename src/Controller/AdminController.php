<?php
// src/Controller/AdminController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin/users', name: 'admin_user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function userList(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findBy([], null, 50);

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/toggle-role/{id}', name: 'admin_toggle_role')]
    #[IsGranted('ROLE_ADMIN')]
    public function toggleUserRole(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_AUTHOR', $roles)) {
            // Retirer ROLE_AUTHOR, ne laisser que ROLE_USER
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_AUTHOR']);
        }

        $em->flush();

        return $this->redirectToRoute('admin_user_list');
    }
}
