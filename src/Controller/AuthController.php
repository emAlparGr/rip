<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController
{
    #[Route('/', name: 'app_auth')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }

    #[Route('/signup', name: 'signup', methods: 'post')]
    public function signUp(Request $request, UserRepository $repository): JsonResponse
    {
        $request_body = json_decode($request->getContent());
        $password = isset($request_body->password) ? $request_body->password : false;
        $confirm_password = isset($request_body->confirm_password) ? $request_body->confirm_password : false;
        if ($confirm_password == false || $confirm_password == false || $confirm_password != $password) {
            return new JsonResponse('failed password validation', 500);
        }
        $existing_emails = $repository->getAllValuesOfColumn('email');
        $email = $request_body->email;
        if (in_array($existing_emails, $email)) {
            return new JsonResponse('user with this email already exists', 500);
        }
        // todo create user and return token
        return $this->json(
            $email
        );
    }

    #[Route('/login', name: 'login', methods: 'post')]
    public function logIn(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }

    #[Route('/logout', name: 'logout', methods: 'delete')]
    public function logOut(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuthController.php',
        ]);
    }
}
