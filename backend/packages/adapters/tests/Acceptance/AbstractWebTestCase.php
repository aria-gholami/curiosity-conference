<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Acceptance;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected Connection $connection;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->connection = $entityManager->getConnection();
        $this->clearDatabase();
    }

    protected function tearDown(): void
    {
        try {
            $this->clearDatabase();
        } finally {
            parent::tearDown();
        }
    }

    private function clearDatabase(): void
    {
        foreach (['user', 'trivia_answer', 'trivia_question', 'questionnaire_answer', 'questionnaire_question', 'questionnaire_settings'] as $table) {
            $this->connection->createQueryBuilder()
                ->delete($table)
                ->executeStatement();
        }
    }
}
