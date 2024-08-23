<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class TokenAuthenticator extends AbstractAuthenticator
{
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        // Check if the request has an Authorization header
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('Authorization');

        if (null === $token) {
            throw new AuthenticationException('No API token provided');
        }

        // Here, you would typically verify the token and extract user information

        return new Passport(
            new UserBadge($token, function ($userIdentifier) {
                // Load the user based on the token (replace with your own logic)
                return $this->userProvider->loadUserByIdentifier($userIdentifier);
            }),
            new PasswordCredentials('dummy_password') // This can be replaced based on your logic
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        // Return a success response
        return null; // Let the request continue if authentication is successful
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
