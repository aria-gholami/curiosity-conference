<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\TriviaQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuestion\PersistController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(PersistController::class)]
final class PersistControllerTest extends AbstractWebTestCase
{
    public function testPersistTriviaIsValid(): void
    {
        $payload = [
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
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
            '/api/v1/trivia/questions',
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
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
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
        ];
        $this->client->request(
            'POST',
            '/api/v1/trivia/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }
}
