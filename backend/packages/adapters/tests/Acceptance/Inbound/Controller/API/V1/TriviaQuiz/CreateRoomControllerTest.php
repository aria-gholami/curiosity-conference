<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\CreateRoomController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(CreateRoomController::class)]
final class CreateRoomControllerTest extends AbstractWebTestCase
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
            [
                'question' => 'test',
                'answers' => [
                    [
                        'answer' => 'testAnswer1',
                        'correct' => true,
                    ],
                    [
                        'answer' => 'testAnswer2',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer3',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer4',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room',
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

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        self::assertArrayHasKey('id', $response);
        self::assertIsString($response['id']);

        $row = $this->connection->fetchAssociative(
            'SELECT * FROM trivia_quiz_room',
        );
        self::assertIsString($row['id']);
        self::assertIsString($row['host_id']);
    }

    public function testCreateQuizRoomWithBadRequest(): void
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
            [
                'question' => 'test',
                'answers' => [
                    [
                        'answer' => 'testAnswer1',
                        'correct' => true,
                    ],
                    [
                        'answer' => 'testAnswer2',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer3',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer4',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'hostId' => '',
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room',
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

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertArrayHasKey('message', $response);
        self::assertSame('Bad Request.', $response['message']);
    }

    public function testCreateQuizRoomWithInvalidApiKey(): void
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
            [
                'question' => 'test',
                'answers' => [
                    [
                        'answer' => 'testAnswer1',
                        'correct' => true,
                    ],
                    [
                        'answer' => 'testAnswer2',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer3',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer4',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'hostId' => $responseUser['id'],
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
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
            [
                'question' => 'test',
                'answers' => [
                    [
                        'answer' => 'testAnswer1',
                        'correct' => true,
                    ],
                    [
                        'answer' => 'testAnswer2',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer3',
                        'correct' => false,
                    ],
                    [
                        'answer' => 'testAnswer4',
                        'correct' => false,
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'hostId' => 'invalid',
            'title' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room',
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

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
        self::assertArrayHasKey('message', $response);
        self::assertSame('Internal Server Error.', $response['message']);
    }
}
