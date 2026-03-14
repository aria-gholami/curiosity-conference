<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\PasswordUpdateHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\User\Model\UserPassword;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class PasswordUpdateController
{
    public function __construct(
        private PasswordUpdateHandler $passwordUpdateHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $userPassword = new UserPassword(
                $data['email'] ?? '',
                $data['password'] ?? '',
                $data['confirmPassword'] ?? '',
            );
            $this->passwordUpdateHandler->handle($userPassword);

            return new JsonResponse([], Response::HTTP_NO_CONTENT);
        } catch (EmptyStringException|InvalidPasswordException $exception) {
            $this->logger->notice('Request data for PUT to /api/v1/user/password is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (UpdateFailedException $exception) {
            $this->logger->notice('User password could not be updated.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
