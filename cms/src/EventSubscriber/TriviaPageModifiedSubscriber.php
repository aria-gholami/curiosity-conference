<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use CuriosityConference\Core\Trivia\PageModifiedBackendSendHandler;
use Psr\Log\LoggerInterface;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class TriviaPageModifiedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private PageModifiedBackendSendHandler $pageModifiedBackendSendHandler,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @codeCoverageIgnore
     *
     * @infection-ignore-all
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            PageModifiedEvent::class => 'onPageModified',
        ];
    }

    public function onPageModified(PageModifiedEvent $event): void
    {
        try {
            $document = $event->getEventPayload();
            $title = $document['title'] ?? '';
            if ('Trivia' !== $title) {
                return;
            }

            $this->pageModifiedBackendSendHandler->handle($document['quizzes'] ?? []);
        } catch (\Throwable $exception) {
            $this->logger->notice('Trivia data could not been sent.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);
        }
    }
}
