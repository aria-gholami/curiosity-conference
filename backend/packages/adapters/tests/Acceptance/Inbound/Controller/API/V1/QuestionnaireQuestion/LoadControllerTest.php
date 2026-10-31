<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\QuestionnaireQuestion;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\QuestionnaireQuestion\LoadController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(LoadController::class)]
final class LoadControllerTest extends AbstractWebTestCase
{
    public function testLoadQuestionsIsValid(): void
    {
        $payload = [
            'questions' => [
                [
                    'question' => 'TestTitle',
                    'answers' => [
                        '1',
                        '2',
                        '3',
                        '4',
                    ],
                ],
            ],
            'showResults' => false,
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

        $this->client->request(
            'GET',
            '/api/v1/questionnaire/questions',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testLoadQuestionsWithUnauthorized(): void
    {
        $payload = [
            'questions' => [
                [
                    'question' => 'TestTitle',
                    'answers' => [
                        '1',
                        '2',
                        '3',
                        '4',
                    ],
                ],
            ],
            'showResults' => false,
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

        $this->client->request(
            'GET',
            '/api/v1/questionnaire/questions',
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        self::assertSame('Unauthorized.', $response['message']);
    }

    public function testLoadQuestionsWithEmptyQuestionData(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/questionnaire/questions',
            server: [
                'HTTP_API_KEY' => '42b1ed2a3b0cdb29b75875f3c88d454d',
            ],
        );
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertSame(0, \count($response['data']));
    }
}
