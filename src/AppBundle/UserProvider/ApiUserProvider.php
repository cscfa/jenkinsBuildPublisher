<?php

namespace AppBundle\UserProvider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Entity\ApiUser;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;

class ApiUserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function supportsClass($class)
    {
        $allowed = ApiUser::class;
        return $class === $allowed || is_subclass_of($class, $allowed);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf(
                    'ApiUserProvider does not support \'%s\' class. Only \'%s\'',
                    get_class($user),
                    ApiUser::class
                )
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param string $class The user class
     *
     * @return EntityRepository
     */
    private function getRepository($class)
    {
        return $this->entityManager->getRepository($class);
    }

    public function loadUserByUsername($username)
    {
        return $this->getRepository(ApiUser::class)
            ->findOneByUsername($username);
    }

    /**
     * @param string $apiKey The user api key
     *
     * @return ApiUser
     */
    public function loadUserByApikey($apiKey)
    {
        return $this->getRepository(ApiUser::class)
            ->findOneByApiKey($apiKey);
    }
}
