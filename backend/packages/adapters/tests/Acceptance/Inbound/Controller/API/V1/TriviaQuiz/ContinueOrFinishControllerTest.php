<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ContinueOrFinishController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(ContinueOrFinishController::class)]
final class ContinueOrFinishControllerTest extends AbstractWebTestCase
{
    public function testContinueQuestionIsValid(): void
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
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/continue',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testContinueQuestionWithBadRequest(): void
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
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/start',
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
            'POST',
            '/api/v1/trivia/quiz/room/continue',
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

    public function testContinueQuestionWithInvalidApiToken(): void
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
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/continue',
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

    public function testContinueQuestionWithInternalServerError(): void
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
            'userId' => $responseUser['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/join',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => $responseQuizRoom['id'],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/start',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizRoomId' => 'invalid',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz/room/continue',
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
