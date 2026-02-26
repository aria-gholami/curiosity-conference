<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\DeleteController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(DeleteController::class)]
final class DeleteControllerTest extends AbstractWebTestCase
{
    public function testDeleteQuizIsValid(): void
    {
        $payload = [
            'title' => 'testDelete',
            'description' => 'testDelete',
            'category' => 'testDelete',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'DELETE',
            '/api/v1/quiz?title=testDelete',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteQuizWithBadRequest(): void
    {
        $payload = [
            'title' => 'testDelete',
            'description' => 'testDelete',
            'category' => 'testDelete',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'DELETE',
            '/api/v1/quiz',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
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

    public function testDeleteQuizWithUnauthorized(): void
    {
        $payload = [
            'title' => 'testDelete',
            'description' => 'testDelete',
            'category' => 'testDelete',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'DELETE',
            '/api/v1/quiz?title=testDelete',
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

    public function testDeleteQuizWithNotExistingTitle(): void
    {
        $this->client->request(
            'DELETE',
            '/api/v1/quiz?title=testDelete',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
