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
    private $realName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string[]
     */
    private $roles = [];

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $login;

    public function __construct($id, $login, $realName, $email, $token)
    {
        $this->realName = $realName;
        $this->email = $email;
        $this->token = $token;
        $this->id = $id;
        $this->login = $login;
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
        return $this->realName;
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

    /**
     * {@inheritdoc}
     */
    public function getGitUserName()
    {
        if (!empty($this->realName)) {
            return $this->realName;
        }

        return $this->login;
    }

    /**
     * {@inheritdoc}
     */
    public function getGitUserEmail()
    {
        return $this->getEmail();
    }
}
