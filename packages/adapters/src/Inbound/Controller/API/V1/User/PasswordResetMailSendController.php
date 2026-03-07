<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\PasswordResetMailSendHandler;
use CuriosityConference\Domain\Exception\CouldNotSentMailException;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Model\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PasswordResetMailSendController
{
    public function __construct(
        private PasswordResetMailSendHandler $passwordResetMailSendHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $email = new Email(
                $data['email'] ?? '',
            );
            $this->passwordResetMailSendHandler->handle($email);

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for /api/v1/password/reset is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (CouldNotSentMailException $exception) {
            $this->logger->notice('Reset password mail could not be sent.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
