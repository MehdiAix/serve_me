<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): Response
    {
        return $this->json(['message' => 'Welcome to your new controller!', 'path' => 'src/Controller/ApiLoginController.php',]);
    }

    #[Route(path: '/api/profile', name: 'profile', methods: ['GET'])]
    public function profile(): Response
    {
        $user = $this->getUser();
        if ($user !== null) {
            return $this->json(data: ['username' => $user->getUsername(), 'roles' => $user->getRoles(),], status: Response::HTTP_OK);
        }
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/authenticate', name: 'authenticate', methods: ['GET'])]
    public function authenticate(): JsonResponse
    {
        return $this->json(true);
    }


}
