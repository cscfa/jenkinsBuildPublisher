<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiUser implements UserInterface
{
    /**
     * @var guid
     */
    private $id;
    private $username;
    private $password;
    private $apiKey;
    private $roles;

    public function __construct(
        $username,
        $apiKey,
        $password = null,
        array $roles = array()
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->apiKey = $apiKey;
        $this->roles = $roles;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = array();
        foreach ($this->roles as $role) {
            if ($role instanceof Role) {
                array_push($roles, $role->getLabel());
            }
        }
        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return ApiUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return ApiUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set apiKey
     *
     * @param string $apiKey
     *
     * @return ApiUser
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get id
     *
     * @return guid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return ApiUser
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }
}
