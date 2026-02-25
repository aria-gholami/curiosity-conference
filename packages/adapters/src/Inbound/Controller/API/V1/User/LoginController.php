<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Inbound\Controller\API\V1\User;

use CuriosityConference\Application\Port\Inbound\User\LoginHandler;
use CuriosityConference\Domain\Exception\EmptyStringException;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class LoginController
{
    public function __construct(
        private LoginHandler $loginHandler,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $userLogin = new UserLogin(
                $request->query->getString('email'),
                $request->query->getString('password'),
            );
            $id = $this->loginHandler->handle($userLogin);

            return new JsonResponse(['id' => $id], Response::HTTP_OK);
        } catch (EmptyStringException $exception) {
            $this->logger->notice('Request data for /api/v1/user/login is invalid.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Bad Request.'], Response::HTTP_BAD_REQUEST);
        } catch (UserLoginFailedException $exception) {
            $this->logger->notice('User login failed.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            return new JsonResponse(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
