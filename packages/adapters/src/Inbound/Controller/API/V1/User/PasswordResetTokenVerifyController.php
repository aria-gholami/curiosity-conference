<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\PasswordResetTokenVerifyHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Exception\InvalidPasswordResetTokenException;
use CuriosityConference\Domain\User\Exception\PasswordResetTokenVerifyFailedException;
use CuriosityConference\Domain\User\Model\PasswordResetToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PasswordResetTokenVerifyController
{
    public function __construct(
        private PasswordResetTokenVerifyHandler $passwordResetTokenVerifyHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $passwordResetToken = new PasswordResetToken(
                $request->query->getInt('token'),
                $request->query->getString('id'),
            );
            $verified = $this->passwordResetTokenVerifyHandler->handleAndGet($passwordResetToken);

            return new JsonResponse([
                'verified' => $verified,
            ], Response::HTTP_OK);
        } catch (EmptyStringException|InvalidPasswordResetTokenException $exception) {
            $this->logger->notice('Request data for GET to /api/v1/password/reset is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PasswordResetTokenVerifyFailedException $exception) {
            $this->logger->notice('Password reset token could not be verified.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
