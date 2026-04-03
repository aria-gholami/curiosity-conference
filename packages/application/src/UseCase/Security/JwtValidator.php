<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\Security;

use CuriosityConference\Application\Port\Inbound\Security\JwtValidator as JwtValidatorInterface;
use CuriosityConference\Application\Port\Outbound\Security\JwtUserToken;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Domain\Exception\InvalidJwtException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

final readonly class JwtValidator implements JwtValidatorInterface
{
    public const string HEADER_NAME = 'Authorization';

    public function __construct(
        private JwtUserToken $jwtUserToken,
        private ByEmailLoader $byEmailLoader,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidJwtException
     */
    #[\Override]
    public function validateRequest(Request $request): void
    {
        try {
            $path = $request->getPathInfo();
            $method = $request->getMethod();
            $isLogin = '/api/v1/user/login' === $path && 'POST' === $method;
            $isUserCreate = '/api/v1/user' === $path && 'POST' === $method;
            $isPasswordReset = '/api/v1/password/reset' === $path || '/api/v1/user/password' === $path;
            if ($isLogin || $isUserCreate || $isPasswordReset) {
                $this->logger->info('Skip jwt validation.', ['isLogin' => $isLogin, 'isUserCreate' => $isUserCreate]);

                return;
            }

            if (false === $request->headers->has(self::HEADER_NAME)) {
                $this->logger->notice('No authorization header found in request.', ['required-header' => self::HEADER_NAME]);
                throw InvalidJwtException::headerIsMissing();
            }

            /** @var string $authorization */
            $authorization = $request->headers->get(self::HEADER_NAME);
            if (!\str_starts_with($authorization, 'Bearer ')) {
                $this->logger->notice('No bearer token header found in request.', ['required-header' => self::HEADER_NAME]);
                throw InvalidJwtException::invalidJwt();
            }

            $token = \substr($authorization, 7);
            if ('' === $token) {
                $this->logger->notice('Bearer token is undefined.', ['required-header' => self::HEADER_NAME]);
                throw InvalidJwtException::invalidJwt();
            }

            $user = $this->jwtUserToken->decode($token);
            $userByDatabase = $this->byEmailLoader->load($user->getEmail());
            $isRoomCreated = '/api/v1/quiz/room' === $path && 'POST' === $method;
            $isRoomStarted = '/api/v1/quiz/room/start' === $path && 'POST' === $method;
            $isRoomContinued = '/api/v1/quiz/room/continue' === $path && 'POST' === $method;
            $isParticipantDeleted =
                \str_starts_with($path, '/api/v1/quiz/room/')
                && \str_contains($path, '/participant/')
                && 'DELETE' === $method;
            if (($isRoomCreated || $isRoomStarted || $isRoomContinued || $isParticipantDeleted) && !$userByDatabase->isAdmin()) {
                $this->logger->notice('User is not admin.');
                throw InvalidJwtException::invalidRole();
            }
        } catch (\Throwable $exception) {
            $this->logger->notice('Bearer token is invalid.', ['required-header' => self::HEADER_NAME]);
            $this->logger->debug('Bearer token is invalid.', [
                'required-header' => self::HEADER_NAME,
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw InvalidJwtException::invalidJwt();
        }
    }
}
