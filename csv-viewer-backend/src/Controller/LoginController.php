<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        PasswordHasherFactoryInterface $passwordEncoder,
        UserProviderInterface $userProvider
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        // Load the user based on the username
        try {
            $user = $userProvider->loadUserByIdentifier($username);
        } catch (AuthenticationException $exception) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        // Validate the password
        if (!$passwordEncoder->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        // Generate a JWT token or custom token logic (this is a placeholder)
        $token = 'your_generated_token_here';

        return $this->json(['token' => $token]);
    }
}
