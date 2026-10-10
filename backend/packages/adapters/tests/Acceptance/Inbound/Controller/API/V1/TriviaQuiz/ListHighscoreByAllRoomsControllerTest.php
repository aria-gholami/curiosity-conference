<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\TriviaQuiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\TriviaQuiz\ListHighscoreByAllRoomsController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Response;

#[Group('Acceptance')]
#[CoversNothing(ListHighscoreByAllRoomsController::class)]
final class ListHighscoreByAllRoomsControllerTest extends AbstractWebTestCase
{
    public function testListQuizUserIsValid(): void
    {
        $this->client->request(
            'GET',
            '/api/v1/trivia/quiz/room/highscores',
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
        self::assertArrayHasKey('data', $response);
        self::assertCount(0, $response['data']);
    }
}
