<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends AbstractController
{
    #[Route('/auth/signup', name: 'api_signup', methods: ['POST'])]
    public function signup(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $request_body = json_decode($request->getContent());
        $password = isset($request_body->password) ? $request_body->password : false;
        $confirm_password = isset($request_body->confirm_password) ? $request_body->confirm_password : false;
        $email = isset($request_body->email) ? $request_body->email : false;
        $name = isset($request_body->name) ? $request_body->name : false;

        if ($confirm_password == false || $password == false || $confirm_password != $password) {
            return new JsonResponse(['error' => 'Подтверждение пароля не совпадает'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!$email) {
            return new JsonResponse(['error' => 'email не указан'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!$name) {
            return new JsonResponse(['error' => 'имя нового пользователя не указано'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $existing_user = $repository->findOneBy(['email' => $email]);
        if ($existing_user) {
            return new JsonResponse(['error' => 'Пользователь с таким email уже существует'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );
        $user->setPassword($hashedPassword);
        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $user->setApiToken($token);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse(['success' => 'Новый пользователь зарегистрирован'], JsonResponse::HTTP_CREATED);
        $response->headers->set('LAB-TOKEN', $token);
        $response->headers->setCookie(Cookie::create('session', $token));

        return $response;
    }

    #[Route('/auth/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $request_body = json_decode($request->getContent());
        $email = isset($request_body->email) ? $request_body->email : false;
        $password = isset($request_body->password) ? $request_body->password : false;

        if (!$email) {
            return new JsonResponse(['error' => 'email не указан'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!$password) {
            return new JsonResponse(['error' => 'password не указан'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $repository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'Нет User с таким email'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $password_valid = $passwordHasher->isPasswordValid($user, $password);

        if (!$password_valid) {
            return new JsonResponse(['error' => 'Неверный пароль'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $token = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $user->setApiToken($token);

        $entityManager->persist($user);
        $entityManager->flush();

        $response = new JsonResponse(['success' => 'Вход выполнен'], JsonResponse::HTTP_CREATED);
        $response->headers->set('LAB-TOKEN', $token);
        $response->headers->setCookie(Cookie::create('session', $token));

        return $response;
    }

    #[Route('/api/info', name: 'api_info', methods: ['GET'])]
    public function info(Request $request, UserRepository $repository): JsonResponse
    {
        $token = $request->headers->get('LAB-TOKEN');
        $user = $repository->findOneBy(['apiToken' => $token]);
        return new JsonResponse([
            'name' => $user->getName(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail()
        ],
            JsonResponse::HTTP_OK
        );
    }
}
