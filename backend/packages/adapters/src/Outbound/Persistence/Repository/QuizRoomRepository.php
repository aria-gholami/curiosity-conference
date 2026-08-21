<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\Question;
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
    public function update(Id $quizId, Question $currentQuestion, string $status): void
    {
        /** @var ?QuizRoom $quizRoom */
        $quizRoom = $this->findOneBy(['id' => $quizId->value]);
        if (!$quizRoom instanceof QuizRoom) {
            throw UpdateFailedException::updateFailed(QuizRoom::class);
        }

        $quizRoom->setStatus($status);
        $quizRoom->setCurrentQuestion($currentQuestion);
        $this->getEntityManager()->flush();
    }

    public function remove(QuizRoom $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findByParticipantId(string $userId): ?QuizRoom
    {
        return $this->createQueryBuilder('qr')
            ->innerJoin('qr.participants', 'p')
            ->andWhere('p.id = :userId')
            ->andWhere('qr.status != :status')
            ->setParameter('userId', $userId, 'uuid')
            ->setParameter('status', QuizRoom::STATUS_CLOSED)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
