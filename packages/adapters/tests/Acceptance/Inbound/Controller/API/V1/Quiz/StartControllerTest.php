<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\StartController;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(StartController::class)]
final class StartControllerTest extends AbstractWebTestCase
{
    public function testStartQuizRoomIsValid(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->connection->executeStatement(
            'UPDATE user SET is_admin = 1',
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
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'PUT',
            '/api/v1/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM quiz_room',
        );
        self::assertSame(QuizRoom::STATUS_STARTED, $row['status']);
    }

    public function testStartQuizRoomWithBadRequest(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->connection->executeStatement(
            'UPDATE user SET is_admin = 1',
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
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => '',
        ];
        $this->client->request(
            'PUT',
            '/api/v1/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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

    public function testStartQuizRoomWithInvalidApiToken(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->connection->executeStatement(
            'UPDATE user SET is_admin = 1',
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
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'PUT',
            '/api/v1/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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

    public function testStartQuizRoomWithInternalServerError(): void
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
        $responseUser = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $this->connection->executeStatement(
            'UPDATE user SET is_admin = 1',
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
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $responseQuizRoom = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'quizRoomId' => 'invalid',
        ];
        $this->client->request(
            'PUT',
            '/api/v1/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
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
