<?php

declare(strict_types=1);

namespace CuriosityConference\Application\UseCase\User;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Application\Port\Inbound\User\DetailsGetHandler as DetailsGetHandlerInterface;
use CuriosityConference\Application\Port\Outbound\User\ByIdLoader;
use CuriosityConference\Domain\User\Model\Id;

/**
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
final readonly class DetailsGetHandler implements DetailsGetHandlerInterface
{
    public function __construct(
        private ByIdLoader $byIdLoader,
    ) {
    }

    #[\Override]
    public function handleAndGet(Id $id): User
    {
        return $this->byIdLoader->load($id->value);
    }
}
