<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Application\Port\Inbound\User\LoginHandler as LoginHandlerInterface;
use CuriosityConference\Application\Port\Outbound\Security\Argon2idHasher;
use CuriosityConference\Application\Port\Outbound\User\ByEmailLoader;
use CuriosityConference\Domain\User\Exception\UserLoginFailedException;
use CuriosityConference\Domain\User\Model\UserLogin;
use Psr\Log\LoggerInterface;

final readonly class LoginHandler implements LoginHandlerInterface
{
    public function __construct(
        private ByEmailLoader $byEmailLoader,
        private Argon2idHasher $argon2idHasher,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws UserLoginFailedException
     */
    #[\Override]
    public function handle(UserLogin $userLogin): string
    {
        try {
            $user = $this->byEmailLoader->load($userLogin->email);
            $isPasswordSame = $this->argon2idHasher->verify($userLogin->password, $user->getPassword());
            if (!$isPasswordSame) {
                throw UserLoginFailedException::loginFailed($userLogin->email);
            }

            return $user->getId();
        } catch (\Throwable $exception) {
            $this->logger->notice('User login failed.', [
                'exceptionMessage' => $exception->getMessage(),
            ]);

            throw UserLoginFailedException::loginFailed($userLogin->email);
        }
    }
}
