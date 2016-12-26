<?php
namespace CsrfTokenStorage;


use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class ArrayCsrfTokenStorage implements TokenStorageInterface
{
    protected $tokenCollection = array();

    public function getToken($tokenId)
    {
        return $this->tokenCollection[$tokenId];
    }

    public function setToken($tokenId, $token)
    {
        $this->tokenCollection[$tokenId] = $token;
    }

    public function removeToken($tokenId)
    {
        unset($this->tokenCollection[$tokenId]);
    }

    public function hasToken($tokenId)
    {
        return isset($this->tokenCollection[$tokenId]);
    }
}