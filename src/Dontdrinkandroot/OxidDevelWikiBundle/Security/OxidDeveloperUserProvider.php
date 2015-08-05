<?php

namespace Dontdrinkandroot\OxidDevelWikiBundle\Security;

use Dontdrinkandroot\OxidDevelWikiBundle\Model\User;
use Github\Api\CurrentUser;
use Github\Api\Organization as OrganizationApi;
use Github\Client;
use Github\HttpClient\CachedHttpClient;
use HWI\Bundle\OAuthBundle\OAuth\Response\AbstractUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OxidDeveloperUserProvider implements UserProviderInterface, OAuthAwareUserProviderInterface
{

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        if (!$response instanceof AbstractUserResponse) {
            throw new \RuntimeException('Unsupported Response Class ' . get_class($response));
        }

        $client = new Client(new CachedHttpClient(['cache_dir' => '/tmp/github-api-cache']));
        $accessToken = $response->getAccessToken();
        $client->authenticate($accessToken, Client::AUTH_HTTP_TOKEN);

//        $teams = $this->getOxidTeams($client);

        /** @var CurrentUser $userApi */
        $userApi = $client->api('currentUser');
        $teams = $userApi->teams();
        $githubEmails = $userApi->emails()->all();
        $email = $this->findPrimaryEmail($githubEmails);

        $id = $response->getResponse()['id'];
        $login = $response->getResponse()['login'];
        $realName = $response->getResponse()['name'];

        $user = new User($id, $login, $realName, $email, $accessToken);

        if ($this->isCommunityManagemementMember($teams) | $this->isDevelopmentWatchersMember($teams)) {
            $user->addRole('ROLE_WATCHER');
        }

        if ($this->isDevelopmentCommittersMembers($teams)) {
            $user->addRole('ROLE_COMMITTER');
        }

        if ($this->isAdminMember($teams)) {
            $user->addRole('ROLE_ADMIN');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        throw new \RuntimeException();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Dontdrinkandroot\\OxidDevelWikiBundle\\Model\\User';
    }

    /**
     * @param array $emails
     *
     * @return string
     */
    private function findPrimaryEmail(array $emails)
    {
        foreach ($emails as $email) {
            if ($email['primary'] === true) {
                return $email['email'];
            }
        }

        throw new \RuntimeException('No valid eMail found');
    }

    /**
     * @param array $teams
     *
     * @return bool
     */
    private function isDevelopmentWatchersMember(array $teams)
    {
        return $this->isMember($teams, 1467821);
    }

    /**
     * @param array $teams
     *
     * @return bool
     */
    private function isDevelopmentCommittersMembers(array $teams)
    {
        return $this->isMember($teams, 1223243);
    }

    /**
     * @param array $teams
     *
     * @return bool
     */
    private function isCommunityManagemementMember(array $teams)
    {
        return $this->isMember($teams, 1286688);
    }

    /**
     * @param array $teams
     *
     * @return bool
     */
    private function isAdminMember(array $teams)
    {
        return $this->isMember($teams, 333525);
    }

    /**
     * @param array $teams
     * @param int   $id
     *
     * @return bool
     */
    private function isMember(array $teams, $id)
    {
        foreach ($teams as $team) {
            if ($team['id'] === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Client $client
     *
     * @return \Guzzle\Http\EntityBodyInterface|mixed|string
     */
    private function getOxidTeams($client)
    {
        /** @var OrganizationApi $organizationApi */
        $organizationApi = $client->api('organizations');
        $teams = $organizationApi->teams()->all('OXID-eSales');

        return $teams;
    }
}
