<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use CuriosityConference\Core\Trivia\ExecutedCodeByCLIChecker;
use CuriosityConference\Core\Trivia\PageModifiedBackendSendHandler;
use Psr\Log\LoggerInterface;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class TriviaPageModifiedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ExecutedCodeByCLIChecker $executedCodeByCLIChecker,
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

    /**
     * @throws BadRequestHttpException
     */
    public function onPageModified(PageModifiedEvent $event): void
    {
        try {
            $document = $event->getEventPayload();
            $title = $document['title'] ?? '';
            if ('Trivia' !== $title || $this->executedCodeByCLIChecker->check()) {
                return;
            }

            $this->pageModifiedBackendSendHandler->handle($document['quizzes'] ?? []);
        } catch (\Throwable $exception) {
            $this->logger->notice('Trivia data could not been sent.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw new BadRequestHttpException(
                'Trivia data could not be persisted: ' . $exception->getMessage(),
                $exception,
            );
        }
    }
}
