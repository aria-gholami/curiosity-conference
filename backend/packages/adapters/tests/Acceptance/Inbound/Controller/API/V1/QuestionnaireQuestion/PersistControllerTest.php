<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion\PersistController;
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
                'question' => 'TestTitle',
                'answers' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/questionnaire/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }

    public function testPersistTriviaWithUnauthorized(): void
    {
        $payload = [
            [
                'question' => 'TestTitle',
                'answers' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/questionnaire/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testPersistTriviaWithBadRequestAndNoQuestion(): void
    {
        $payload = [
            [
                'question' => '',
                'answers' => [
                    '1',
                    '2',
                    '3',
                    '4',
                ],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/questionnaire/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testPersistTriviaWithBadRequestAndNoAnswers(): void
    {
        $payload = [
            [
                'question' => 'TestTitle',
                'answers' => [],
            ],
        ];
        $this->client->request(
            'POST',
            '/api/v1/questionnaire/questions',
            server: [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
            content: \json_encode($payload, \JSON_THROW_ON_ERROR),
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
