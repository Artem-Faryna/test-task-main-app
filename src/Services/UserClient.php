<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\UserFilters;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\User;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class UserClient
{
    private const USER_BASE_PATH = '/users';
    private const USER_MESSAGE_TYPE_SUCCESS = 'success';
    private const USER_MESSAGE_TYPE_ERROR = 'warning';
    private const USER_SAVED_MESSAGE = 'Your changes were saved!';
    private const USER_ERROR_MESSAGE = 'Something wrong with the server, please try again later';

    private HttpClientInterface $httpClient;
    private string $endpoint;
    private UserSerializerService $serializer;

    public function __construct(HttpClientInterface $httpClient, string $apiUrl, UserSerializerService $serializer)
    {
        $this->httpClient = $httpClient;
        $this->endpoint = $apiUrl . self::USER_BASE_PATH;
        $this->serializer = $serializer;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUsers(?UserFilters $userFilters): array
    {
        $query = $userFilters
            ? sprintf('?email=%s&username=%s', $userFilters->getEmail(), $userFilters->getUsername())
            : '';

        $response = $this->httpClient->request('GET', $this->endpoint . $query);

        return $this->serializer->deserializeList($response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getUser(int $id): User
    {
        $response = $this->httpClient->request('GET', $this->endpoint . "/$id");

        return $this->serializer->deserialize($response->getContent());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function create(User $user): array
    {
        $response = $this->httpClient->request('POST', $this->endpoint, [
            'body' => [
                'user' => $this->serializer->serialize($user),
            ]
        ]);

        return $this->readResponseStatus($response);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function edit(User $user): array
    {
        $response = $this->httpClient->request('PUT', $this->endpoint . '/' . $user->getId() . '/edit', [
            'body' => [
                'user' => $this->serializer->serialize($user),
            ]
        ]);

        return $this->readResponseStatus($response);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[ArrayShape(['type' => "string", 'message' => "string"])]
    private function readResponseStatus(ResponseInterface $response): array
    {
        $response = json_decode($response->getContent(), true);

        if ($response['status'] === 'error') {
            return [
                'type' => self::USER_MESSAGE_TYPE_ERROR,
                'message' => $message['error'] ?? self::USER_ERROR_MESSAGE,
            ];
        }

        return [
            'type' => self::USER_MESSAGE_TYPE_SUCCESS,
            'message' => self::USER_SAVED_MESSAGE,
        ];
    }
}
