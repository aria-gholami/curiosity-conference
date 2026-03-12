<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\User;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\User\PasswordResetMailSendController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(PasswordResetMailSendController::class)]
final class PasswordResetMailSendControllerTest extends AbstractWebTestCase
{
    public function testResetPasswordIsValid(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        $payload = [
            'email' => 'test@web.de',
        ];
        $this->client->request(
            'POST',
            '/api/v1/password/reset',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testResetPasswordWithBadRequest(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        $payload = [
        ];
        $this->client->request(
            'POST',
            '/api/v1/password/reset',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
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

    public function testResetPasswordWithUnauthorized(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        $payload = [
            'email' => 'test@web.de',
        ];
        $this->client->request(
            'POST',
            '/api/v1/password/reset',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
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

    public function testResetPasswordWithInternalServerError(): void
    {
        $payload = [
            'firstName' => 'test',
            'lastName' => 'test',
            'email' => 'test@web.de',
            'password' => 'Test123!',
            'confirmPassword' => 'Test123!',
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
        $payload = [
            'email' => 'test2@web.de',
        ];
        $this->client->request(
            'POST',
            '/api/v1/password/reset',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $response['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
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
