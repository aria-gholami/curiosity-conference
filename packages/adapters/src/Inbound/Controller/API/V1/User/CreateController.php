<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\CreateHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\Exception\InvalidEmailException;
use CuriosityConference\Domain\Exception\InvalidPasswordException;
use CuriosityConference\Domain\Exception\PersistFailedException;
use CuriosityConference\Domain\User\Model\UserRegistration;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateController
{
    public function __construct(
        private CreateHandler $createHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = \json_decode($request->getContent(), true);
            $email = $data['email'] ?? '';
            $userRegistration = new UserRegistration(
                $data['firstName'] ?? '',
                $data['lastName'] ?? '',
                \lcfirst($email),
                $data['password'] ?? '',
                $data['confirmPassword'] ?? '',
            );
            $userLoginResponse = $this->createHandler->handle($userRegistration);

            return new JsonResponse(['id' => $userLoginResponse->id, 'token' => $userLoginResponse->token], Response::HTTP_CREATED);
        } catch (EmptyStringException|InvalidEmailException|InvalidPasswordException $exception) {
            $this->logger->notice('Request data for /api/v1/user is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (PersistFailedException $exception) {
            $this->logger->notice('User could not be persisted.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Internal Server Error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
