<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\DetailsGetController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(DetailsGetController::class)]
final class DetailsGetControllerTest extends AbstractWebTestCase
{
    public function testGetUserIsValid(): void
    {
        $payload = [
            'userName' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/user',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->client->request(
            'GET',
            '/api/v1/user?id=' . $response['id'],
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSame('test', $response['userName']);
    }

    public function testGetUserWithBadRequest(): void
    {
        $payload = [
            'userName' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/user',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->client->request(
            'GET',
            '/api/v1/user?id=',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertArrayHasKey('message', $response);
        self::assertSame('Bad Request.', $response['message']);
    }

    public function testGetUserWithInvalidApiKey(): void
    {
        $payload = [
            'userName' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/user',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->client->request(
            'GET',
            '/api/v1/user?id=' . $response['id'],
            server: [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertArrayHasKey('message', $response);
        self::assertSame('Unauthorized.', $response['message']);
    }

    public function testGetUserWithMissingUser(): void
    {
        $payload = [
            'userName' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/user',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->client->request(
            'GET',
            '/api/v1/user?id=invalid',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        self::assertArrayHasKey('message', $response);
        self::assertSame('Internal Server Error.', $response['message']);
    }
}
