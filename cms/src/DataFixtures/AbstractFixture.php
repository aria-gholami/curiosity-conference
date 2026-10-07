<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Sulu\Content\Domain\Model\WorkflowInterface;
use Sulu\Messenger\Infrastructure\Symfony\Messenger\FlushMiddleware\EnableFlushStamp;
use Sulu\Page\Application\Message\ApplyWorkflowTransitionPageMessage;
use Sulu\Page\Application\Message\CreatePageMessage;
use Sulu\Page\Application\Message\ModifyPageMessage;
use Sulu\Page\Domain\Model\PageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
abstract class AbstractFixture extends Fixture
{
    protected const string WEBSPACE_KEY = 'website';
    protected const string LOCALE_EN = 'en';
    protected const string LOCALE_DE = 'de';

    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    protected function createPage(PageInterface $website, array $triviaData): Envelope
    {
        return $this->messageBus->dispatch(
            new Envelope(
                new CreatePageMessage(
                    self::WEBSPACE_KEY,
                    $website->getUuid(),
                    $triviaData,
                ),
                [
                    new EnableFlushStamp(),
                ],
            ),
        );
    }

    /**
     * @throws ExceptionInterface
     */
    protected function modifyPage(PageInterface $trivia, array $triviaData): void
    {
        $this->messageBus->dispatch(
            new Envelope(
                new ModifyPageMessage(
                    [
                        'uuid' => $trivia->getUuid(),
                    ],
                    $triviaData,
                ),
                [
                    new EnableFlushStamp(),
                ],
            ),
        );
    }

    protected function publishPage(string $uuid, string $locale): void
    {
        $this->messageBus->dispatch(new Envelope(
            new ApplyWorkflowTransitionPageMessage(
                ['uuid' => $uuid],
                $locale,
                WorkflowInterface::WORKFLOW_TRANSITION_PUBLISH,
            ),
            [new EnableFlushStamp()],
        ));
    }
}
