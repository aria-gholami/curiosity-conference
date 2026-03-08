<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\PasswordReset;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PasswordReset>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class PasswordResetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordReset::class);
    }

    public function remove(PasswordReset $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function removeByUser(User $user): void
    {
        $tokens = $this->findBy(['user' => $user]);
        foreach ($tokens as $token) {
            $this->getEntityManager()->remove($token);
        }
    }
}
