<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;


class CustomAuthenticator extends AbstractAuthenticator
{
    private $userProvider;
    private $passwordHasher;

    public function __construct(UserProviderInterface $userProvider, PasswordHasherFactoryInterface $passwordHasher)
    {
        $this->userProvider = $userProvider;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && $request->attributes->get('_route') === 'api_login';
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (!$username || !$password) {
            throw new AuthenticationException('Username and password must be provided');
        }

        $user = $this->userProvider->loadUserByIdentifier($username);

        if (!$user) {
            throw new AuthenticationException('User not found');
        }

        // Verify the password
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials');
        }

        return new Passport(
            new UserBadge($username, function ($userIdentifier) {
                return $this->userProvider->loadUserByIdentifier($userIdentifier);
            }),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        return null; // Allow the request to continue if authentication is successful
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Authentication failed',
            'message' => $exception->getMessage()
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Authentication Required',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
