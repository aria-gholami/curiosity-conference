<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Outbound\Persistence\Repository;

use CuriosityConference\Adapters\Outbound\Persistence\Entity\QuestionnaireSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionnaireSettings>
 *
 * @codeCoverageIgnore
 *
 * @infection-ignore-all
 */
class QuestionnaireSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionnaireSettings::class);
    }

    public function persist(QuestionnaireSettings $questionnaireSettings): void
    {
        $this->getEntityManager()->persist($questionnaireSettings);
        $this->getEntityManager()->flush();
    }
}
