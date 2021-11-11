<?php

namespace App\Services;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;


class PasswordService
{

    /**
     * @param PasswordHasherInterface $userPasswordHash
     */
    public function __construct(HasherUserPasswordHasherInterface $userPasswordHash)
    {
        $this->userPasswordHash = $userPasswordHash;
    }

    /**
     * @param object $entity
     * @param string $password
     * @return string
     */
    public function hashPassowrd($entity,string $password): string
    {
        
        return $this->userPasswordHash->hashPassword($entity,$password);
    }

    /**
     * @param string $password
     * @return int
     */
    public function formatRequirement(string $password)
    {
        return preg_match('#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W)#',$password);
    }
    
}