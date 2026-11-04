<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuestion;
use CuriosityConference\Adapters\Outbound\Persistence\Entity\TriviaQuizRoom;
use CuriosityConference\Domain\Exception\UpdateFailedException;
use CuriosityConference\Domain\TriviaQuiz\Id;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TriviaQuizRoom>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class TriviaQuizRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TriviaQuizRoom::class);
    }

    /**
     * @throws UpdateFailedException
     */
    public function update(Id $quizId, TriviaQuestion $currentTriviaQuestion, string $status): void
    {
        /** @var ?TriviaQuizRoom $quizRoom */
        $quizRoom = $this->findOneBy(['id' => $quizId->value]);
        if (!$quizRoom instanceof TriviaQuizRoom) {
            throw UpdateFailedException::updateFailed(TriviaQuizRoom::class);
        }

        $quizRoom->setStatus($status);
        $quizRoom->setCurrentTriviaQuestion($currentTriviaQuestion);
        $this->getEntityManager()->flush();
    }

    public function remove(TriviaQuizRoom $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneByParticipantId(string $userId): ?TriviaQuizRoom
    {
        $result = $this->createQueryBuilder('qr')
            ->select('qr.id AS id')
            ->innerJoin('qr.triviaParticipants', 'p')
            ->andWhere('p.id = :userId')
            ->andWhere('qr.status != :status')
            ->setParameter('userId', $userId, 'uuid')
            ->setParameter('status', TriviaQuizRoom::STATUS_FINISHED)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);

        return null === $result ? null : $this->findOneByIdWithParticipants((string) $result['id']);
    }

    public function findByParticipantId(string $userId): array
    {
        return $this->createQueryBuilder('qr')
            ->select('qr.id AS id', 'qr.status AS status')
            ->innerJoin('qr.triviaParticipants', 'p')
            ->andWhere('p.id = :userId')
            ->setParameter('userId', $userId, 'uuid')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return TriviaQuizRoom[]
     */
    public function findAllWithParticipants(): array
    {
        return $this->createQueryBuilder('qr')
            ->addSelect('host', 'participants')
            ->innerJoin('qr.host', 'host')
            ->leftJoin('qr.triviaParticipants', 'participants')
            ->getQuery()
            ->getResult();
    }

    public function findOneByIdWithParticipants(string $id): ?TriviaQuizRoom
    {
        return $this->createQueryBuilder('qr')
            ->addSelect('host', 'participants', 'question')
            ->innerJoin('qr.host', 'host')
            ->leftJoin('qr.triviaParticipants', 'participants')
            ->leftJoin('qr.currentTriviaQuestion', 'question')
            ->andWhere('qr.id = :id')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
