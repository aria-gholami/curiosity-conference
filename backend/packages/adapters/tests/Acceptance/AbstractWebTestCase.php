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
    }

    protected function tearDown(): void
    {
        $this->connection->executeStatement('DELETE FROM user;');
        $this->connection->executeStatement('DELETE FROM trivia_answer;');
        $this->connection->executeStatement('DELETE FROM trivia_question;');
        $this->connection->executeStatement('DELETE FROM trivia_quiz;');
    }
}
