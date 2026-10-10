<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TriviaQuestion>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class TriviaQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TriviaQuestion::class);
    }

    public function remove(TriviaQuestion $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }
}
