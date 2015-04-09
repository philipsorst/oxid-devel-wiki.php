<?php

namespace Dontdrinkandroot\OxidDevelWikiBundle\Model;

use Dontdrinkandroot\GitkiBundle\Model\GitUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, GitUserInterface
{

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string[]
     */
    private $roles = [];

    public function __construct($userName, $email, $token)
    {
        $this->userName = $userName;
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->userName;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        /* Noop */
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $role
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }
}
