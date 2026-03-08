<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\User;
use CuriosityConference\Domain\User\Model\UserDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function remove(User $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function updateNameById(UserDetails $userDetails): void
    {
        /** @var ?User $user */
        $user = $this->find($userDetails->id);
        if (!$user instanceof User) {
            return;
        }

        $user->setFirstname($userDetails->firstName);
        $user->setLastname($userDetails->lastName);

        $this->getEntityManager()->flush();
    }
}
