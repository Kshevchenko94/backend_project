<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *
 */
class NewsController extends AbstractController
{
    #[Route('/news', name: 'news')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        die(print_r($user));
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/NewsController.php',
        ]);
    }
}
