<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\LoginHandler as LoginHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Security\Argon2idHasher;
use CuriosityConference\Application\Port\Outbound\Security\JwtUserToken;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use CuriosityConference\Domain\User\Model\UserLoginResponse;
use Psr\Log\LoggerInterface;

final readonly class LoginHandler implements LoginHandlerInterface
{
    public function __construct(
        private ByEmailLoader $byEmailLoader,
        private Argon2idHasher $argon2idHasher,
        private JwtUserToken $jwtUserToken,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UserLoginFailedException
     */
    #[\Override]
    public function handle(UserLogin $userLogin): UserLoginResponse
    {
        try {
            $user = $this->byEmailLoader->load($userLogin->email);
            $isPasswordSame = $this->argon2idHasher->verify($userLogin->password, $user->getPassword());
            if (!$isPasswordSame) {
                throw UserLoginFailedException::loginFailed($userLogin->email);
            }

            return new UserLoginResponse($user->getId(), $this->jwtUserToken->encode($user));
        } catch (\Throwable $exception) {
            $this->logger->notice('User login failed.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw UserLoginFailedException::loginFailed($userLogin->email);
        }
    }
}
