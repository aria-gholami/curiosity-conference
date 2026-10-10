<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TriviaQuiz>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class TriviaQuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TriviaQuiz::class);
    }

    public function remove(TriviaQuiz $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }
}
