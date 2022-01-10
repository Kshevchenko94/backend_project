<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This controller is for sign up a new user in system
 */
class RegistrationController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     * @throws Exception
     */
    #[Route('/registration', name: 'registration', methods: 'POST')]
    public function index(Request $request, UserRepository $userRepository,UserPasswordHasherInterface $passwordHashed): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$userRepository->findOneByUsername($data['username'])) {
            if ($userRepository->createUser($data, $passwordHashed)) {
                return $this->json([
                    'status_code' => Response::HTTP_CREATED,
                    'status_text' => 'Created',
                ]);
            }
            
            throw new Exception("User can't create");
        }
        return $this->json([
            'status_code' => Response::HTTP_CONFLICT,
            'status_text' => 'User already exists',
        ]);
    }
}
