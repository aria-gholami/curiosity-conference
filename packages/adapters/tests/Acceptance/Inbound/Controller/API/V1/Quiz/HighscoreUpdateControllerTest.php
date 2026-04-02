<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance\Inbound\Controller\API\V1\Quiz;

use CuriosityConference\Adapters\Inbound\Controller\API\V1\Quiz\ContinueOrFinishController;
use CuriosityConference\Adapters\Tests\Acceptance\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;

#[Group('Acceptance')]
#[CoversNothing(ContinueOrFinishController::class)]
final class HighscoreUpdateControllerTest extends AbstractWebTestCase
{
    // TODO: Test implementieren, nachdem StartController fertig ist.
    public function testSomething(): void
    {
        self::markTestSkipped('StartController noch nicht fertig.');
    }
}
