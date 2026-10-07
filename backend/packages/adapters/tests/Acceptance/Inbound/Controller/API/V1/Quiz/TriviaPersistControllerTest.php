<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\TriviaPersistController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(TriviaPersistController::class)]
final class TriviaPersistControllerTest extends AbstractWebTestCase
{
    public function testPersistTriviaIsValid(): void
    {
        $payload = [
            [
                'title' => 'Test Quiz',
                'questions' => [
                    [
                        'question' => 'Test Question',
                        'answers' => [
                            [
                                'answer' => 'Test Answer 1',
                                'correct' => true,
                            ],
                            [
                                'answer' => 'Test Answer 2',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 3',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 4',
                                'correct' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quizzes',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPersistTriviaWithBadRequest(): void
    {
        $this->client->request(
            'POST',
            '/api/v1/trivia/quizzes',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPersistTriviaWithUnauthorized(): void
    {
        $payload = [
            [
                'title' => 'Test Quiz',
                'questions' => [
                    [
                        'question' => 'Test Question',
                        'answers' => [
                            [
                                'answer' => 'Test Answer 1',
                                'correct' => true,
                            ],
                            [
                                'answer' => 'Test Answer 2',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 3',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 4',
                                'correct' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quizzes',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            content: \json_encode(null, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testPersistTriviaWithConflict(): void
    {
        $payload = [
            [
                'title' => 'Test Quiz',
                'questions' => [
                    [
                        'question' => 'Test Question',
                        'answers' => [
                            [
                                'answer' => 'Test Answer 1',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 2',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 3',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 4',
                                'correct' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quizzes',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }

    public function testPersistWithInternalServerError(): void
    {
        $payload = [
            [
                'title' => null,
                'questions' => [
                    [
                        'question' => 'Test Question',
                        'answers' => [
                            [
                                'answer' => 'Test Answer 1',
                                'correct' => true,
                            ],
                            [
                                'answer' => 'Test Answer 2',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 3',
                                'correct' => false,
                            ],
                            [
                                'answer' => 'Test Answer 4',
                                'correct' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/quizzes',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
