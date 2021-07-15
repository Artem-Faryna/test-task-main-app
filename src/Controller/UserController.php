<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\UserClient;
use App\Form\Type\UserFromType;
use App\Form\Type\UserFiltersType;
use App\Entity\User;
use Exception;

class UserController extends AbstractController
{
    #[Route('/users', name: 'users_list')]
    public function index(Request $request, UserClient $userClient): Response
    {
        try {
            $form = $this->createForm(UserFiltersType::class);
            $form->handleRequest($request);

            return $this->render('user/users-list.html.twig', [
                'users' => $userClient->getUsers($form->getData()),
                'filters' => $form->createView(),
            ]);
        } catch (Exception $e) {
            return $this->redirectToRoute('error_page');
        }
    }

    #[Route('/users/create', name: 'users_create')]
    public function create(Request $request, UserClient $userClient): Response
    {
        try {
            $form = $this->createForm(UserFromType::class, new User());
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $message = $userClient->create($form->getData());

                $this->addFlash($message['type'], $message['message']);
            }

            return $this->render('user/user-form.html.twig', ['form' => $form->createView()]);
        } catch (Exception $e) {
            return $this->redirectToRoute('error_page');
        }
    }

    #[Route('/users/{id}', name: 'users_edit')]
    public function edit(int $id, Request $request, UserClient $userClient): Response
    {
        try {
            $user = $userClient->getUser($id);
            $form = $this->createForm(UserFromType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $message = $userClient->edit($form->getData());

                $this->addFlash($message['type'], $message['message']);
            }

            return $this->render('user/user-form.html.twig', ['form' => $form->createView()]);
        } catch (Exception $e) {
            return $this->redirectToRoute('error_page');
        }
    }
}
