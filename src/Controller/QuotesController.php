<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\QuoteRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Quote;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

#[Route('/api/quotes')]

class QuotesController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_quotes')]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/QuotesController.php',
        ]);
    }

    #[Route('/qget', name: 'get_quotes', methods: 'get')]
    public function get_quotes(QuoteRepository $repository): JsonResponse
    {
        $list = $repository->findAll();
        $arr = (array) $list;
        foreach ($list as $key => $value) {
            $arr[$key] = [
                'id' => $value->getId(),
                'author' => $value->getAuthor(),
            ];
        }
        return new JsonResponse([
            'quotes' => $arr
        ]);
    }

    #[Route('/qpost', name: 'create_author', methods: 'post')]
    public function createAuthor(Request $request, QuoteRepository $repository, UserRepository $userRepository): JsonResponse
    {
        $quotes = new Quote();
        $quotes->setAuthor($request->request->get('author'));
        $token = $request->headers->get('LAB-TOKEN');
        $user = $userRepository->findOneBy(['apiToken' => $token]);
        $quotes->setUser($user);
        $this->entityManager->persist($quotes);
        $this->entityManager->flush();
        return new JsonResponse([
            "author" => [
                'id' => $quotes->getId(),
                'author' => $quotes->getAuthor(),
            ]
        ]);
    }


    #[Route('/qput/{id}', name: 'put_author', methods: 'put')]
    public function putAuthor($id, Request $request, QuoteRepository $repository): JsonResponse
    {
        $quotes = $repository->find($id);

        $data = json_decode($request->getContent(), true);
        $quotes->setAuthor($data['author']);

        $this->entityManager->flush();
        return new JsonResponse([
            "author" => [
                'id' => $quotes->getId(),
                'author' => $quotes->getAuthor(),
            ]
        ]);
    }

    #[Route('/{id}', name: 'delete_author', methods: 'delete')]
    public function deleteAuthor($id, Request $request, QuoteRepository $repository): JsonResponse
    {
        $quotes = $repository->find($id);
        $this->entityManager->remove($quotes);
        $this->entityManager->flush();
        return new JsonResponse([
            "author" => [
                'id' => $quotes->getId(),
                'author' => $quotes->getAuthor(),
            ]
        ]);
    }
}

