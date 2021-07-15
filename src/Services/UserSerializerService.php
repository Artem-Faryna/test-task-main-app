<?php
declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\User;

class UserSerializerService
{
    private Serializer $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(), new ArrayDenormalizer()],
            [new JsonEncoder()]
        );
    }

    public function deserializeList(string $users): array
    {
        return $this->serializer->deserialize($users, 'App\Entity\User[]', 'json');
    }

    public function deserialize(string $users): User
    {
        return $this->serializer->deserialize($users, User::class, 'json');
    }

    public function serialize(User $user): string
    {
        return $this->serializer->serialize($user, 'json');
    }
}
