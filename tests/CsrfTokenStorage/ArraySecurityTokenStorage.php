<?php
/**
 * Created by PhpStorm.
 * User: aurore
 * Date: 06/01/2017
 * Time: 18:59
 */

namespace CsrfTokenStorage;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ArraySecurityTokenStorage implements TokenStorageInterface
{
    protected $token;

    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}