<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use CuriosityConference\Core\Questionnaire\ExecutedCodeByCLIChecker;
use CuriosityConference\Core\Questionnaire\PageModifiedBackendSendHandler;
use Psr\Log\LoggerInterface;
use Sulu\Page\Domain\Event\PageModifiedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class QuestionnairePageModifiedSubscriber implements EventSubscriberInterface
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
            if ('Questionnaire' !== $title || $this->executedCodeByCLIChecker->check()) {
                return;
            }

            $this->pageModifiedBackendSendHandler->handle($document['questions'] ?? []);
        } catch (\Throwable $exception) {
            $this->logger->notice('Questionnaire data could not been sent.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw new BadRequestHttpException(
                'Questionnaire data could not be persisted: ' . $exception->getMessage(),
                $exception,
            );
        }
    }
}
