<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Question;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Question\CreateController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(CreateController::class)]
final class CreateControllerTest extends AbstractWebTestCase
{
    public function testCreateQuestionIsValid(): void
    {
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
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'question' => 'test',
            'quizId' => $response['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
            'correctAnswerIndex' => 1,
        ];
        $this->client->request(
            'POST',
            '/api/v1/question',
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
            'SELECT * FROM question WHERE title = :title',
            ['title' => 'test'],
        );
        self::assertIsString($row['id']);
        self::assertSame('test', $row['title']);
        $row = $this->connection->fetchAssociative(
            'SELECT * FROM answer WHERE title = :title',
            ['title' => 'testAnswer1'],
        );
        self::assertIsString($row['id']);
        self::assertSame('testAnswer1', $row['title']);
    }

    public function testCreateQuestionWithBadRequest(): void
    {
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
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'question' => 'test',
            'quizId' => $response['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
        ];
        $this->client->request(
            'POST',
            '/api/v1/question',
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

    public function testCreateQuestionWithUnauthorized(): void
    {
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
        $response = \json_decode(
            $this->client->getResponse()->getContent(),
            true,
            flags: \JSON_THROW_ON_ERROR,
        );
        $payload = [
            'question' => 'test',
            'quizId' => $response['id'],
            'answer1' => 'testAnswer1',
            'answer2' => 'testAnswer2',
            'answer3' => 'testAnswer3',
            'answer4' => 'testAnswer4',
            'correctAnswerIndex' => 1,
        ];
        $this->client->request(
            'POST',
            '/api/v1/question',
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
}
