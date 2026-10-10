<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ListHighscoreByRoomController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(ListHighscoreByRoomController::class)]
final class ListHighscoreByRoomControllerTest extends AbstractWebTestCase
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
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz',
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
            'question' => 'test',
            'quizId' => $responseQuiz['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
            'correctAnswerIndex' => 1,
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/question',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
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
            'GET',
            '/api/v1/trivia/quiz/room/' . $responseQuizRoom['id'] . '/highscores',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testContinueQuestionWithInvalidApiKey(): void
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
            '/api/v1/trivia/quiz',
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
            'question' => 'test',
            'quizId' => $responseQuiz['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
            'correctAnswerIndex' => 1,
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/question',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
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
            'GET',
            '/api/v1/trivia/quiz/room/' . $responseQuizRoom['id'] . '/highscores',
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
            'title' => 'test',
            'description' => 'test',
            'category' => 'test',
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quiz',
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
            'question' => 'test',
            'quizId' => $responseQuiz['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
            'correctAnswerIndex' => 1,
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/question',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $responseUser['token'],
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );
        $payload = [
            'quizId' => $responseQuiz['id'],
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
            'GET',
            '/api/v1/trivia/quiz/room/invalid/highscores',
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
