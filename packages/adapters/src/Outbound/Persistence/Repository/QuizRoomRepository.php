<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizRoom>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class QuizRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizRoom::class);
    }
}
