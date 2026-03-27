<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuizRoom;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\Quiz\Id;
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

    /**
     * @throws UpdateFailedException
     */
    public function updateIsStarted(Id $quizId): void
    {
        /** @var ?QuizRoom $quizRoom */
        $quizRoom = $this->findOneBy(['id' => $quizId->value]);
        if (!$quizRoom instanceof QuizRoom) {
            throw UpdateFailedException::updateFailed(QuizRoom::class);
        }

        $quizRoom->setIsStarted(!$quizRoom->isStarted());
        $this->getEntityManager()->flush();
    }
}
