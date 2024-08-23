<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\PasswordHasherFactoryInterface;

class User implements UserInterface
{
    private $password;
    private $roles;

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void {}


    public function getUserIdentifier(): string
    {
        // Return a unique identifier for the user (e.g., username or email)
    }
}
