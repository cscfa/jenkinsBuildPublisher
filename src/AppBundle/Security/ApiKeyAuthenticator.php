<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\UserProvider\ApiUserProvider;

class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface::supportsToken()
     */
    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface::authenticateToken()
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        $apiKey = $token->getCredentials();

        $user = $userProvider->loadUserByApikey($apiKey);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                sprintf('API Key "%s" does not exist.', $apiKey)
            );
        }

        return new PreAuthenticatedToken(
            $user,
            $apiKey,
            $providerKey,
            $user->getRoles()
        );
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface::createToken()
     */
    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->headers->get('apikey');

        if ($apiKey === null) {
            throw new CustomUserMessageAuthenticationException(
                'API Key must be given'
            );
        }

        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface::onAuthenticationFailure()
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        return new JsonResponse(
            strtr($exception->getMessageKey(), $exception->getMessageData()),
            403
        );
    }

}
