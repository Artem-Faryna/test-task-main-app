<?php
declare(strict_types=1);

namespace App\Entity;

class UserFilters
{
    private string|null $username = '';
    private string|null $email = '';

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
