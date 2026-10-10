<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaHighscore;
use CuriosityConference\Domain\TriviaQuiz\QuizRoomHighscore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TriviaHighscore>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class TriviaHighscoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TriviaHighscore::class);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @return QuizRoomHighscore[]
     */
    public function loadFromAllRooms(): array
    {
        return $this->createQueryBuilder('h')
            ->select(\sprintf('NEW %s(qr.title, SUM(h.score))', QuizRoomHighscore::class))
            ->innerJoin('h.triviaQuizRoom', 'qr')
            ->groupBy('qr.id, qr.title')
            ->getQuery()
            ->getResult();
    }
}
