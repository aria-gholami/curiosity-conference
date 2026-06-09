<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ActiveRoomLoadController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(ActiveRoomLoadController::class)]
final class ActiveRoomLoadControllerTest extends AbstractWebTestCase
{
    public function testCreateQuizRoomIsValid(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
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
        $responseQuiz = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $responseQuizRoom = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'GET',
            '/api/v1/quiz/room/user/' . $responseUser['id'],
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateQuizRoomWithEmptyResult(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
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
        $responseQuiz = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $responseQuizRoom = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'GET',
            '/api/v1/quiz/room/user/c0ec7e15-3011-48c3-81c2-a1913b845b1a',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testCreateQuizRoomWithInvalidApiToken(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
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
        $responseQuiz = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $responseQuizRoom = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'GET',
            '/api/v1/quiz/room/user/c0ec7e15-3011-48c3-81c2-a1913b845b1a',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateQuizRoomWithInternalServerError(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
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
        $responseQuiz = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $responseQuizRoom = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $this->client->request(
            'GET',
            '/api/v1/quiz/room/user/invalid',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
