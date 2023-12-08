<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController
{
    #[Route('/', name: 'app_auth')]

    #[Route('/login', name: 'login', methods: 'post')]
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            return $this->json([
                'code' => Response::HTTP_OK,
                'message' => 'See PHPSESSID in cookie',
                'name' => $user->getName(),
            ], Response::HTTP_OK);
        };

        return $this->json([
            'code' => Response::HTTP_UNAUTHORITHED,
            'message' => 'missing credentials',
        ], Response::HTTP_UNAUTHORITHED);
    }

    #[Route('/info')]
    public function getInfo(): Response
    {
        if ($this->getUser() != null) {
            return $this->json([
                'code' => Response::HTTP_OK,
                'isGranted' => true,
            ]);
        }

        return $this->json([
            'code' => Response::HTTP_OK,
            'message' => 'AuthController user not found',
        ]);
    }

    #[Route('/registration', name: 'apiRegistration', methods: 'post')]
    public function registration(Request $request, UserPasswordHasherInterface $hasher, ManagerRegistry $registry): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        //dd($content);

        $user = new User();
        $user->setName($content['name']);
        $user->setPassword($haser->hashPassword($user, $content['password']));
        $user->setEmail($content['email']);
        
        $em = $registry->getManager('users');
        $em->persist($user);
        $em->flash();
        return $this->json([
            'code' => Response::HTTP_OK,
        ]);
    }

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
        $email = $request_body->email;
        $password = $request_body->password;
        $confirm_password = $request_body->confirm_password;

        // Проверка, что пароль и подтверждение пароля совпадают
        if ($password !== $confirm_password) {
            return new JsonResponse(['error' => 'Пароль и подтверждение пароля не совпадают'], 400);
        }

        // Проверка уникальности email
        $existing_user = $repository->findOneBy(['email' => $email]);
        if ($existing_user) {
            return new JsonResponse(['error' => 'Пользователь с таким email уже существует'], 400);
        }


        return new JsonResponse(['message' => 'Вы успешно зарегистрировались'], 201);
    }


    #[Route('/logout', name: 'logout', methods: 'delete')]
    public function logOut(): JsonResponse
    {
        // Ваш код для разлогинивания пользователя (если необходимо)
        // ...

        return $this->json(['message' => 'Вы успешно разлогинились']);
    }
}


/*
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
*/
