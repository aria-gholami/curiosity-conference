<?php

declare(strict_types=1);

namespace CuriosityConference\Adapters\Tests\Unit\Mocks;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Cache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Proxy\ProxyFactory;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\FilterCollection;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnitOfWork;

final class EntityManagerMock implements EntityManagerInterface
{
    public array $inputPersistedObject;
    public ?\Exception $throwPersistException = null;
    public bool $isFlushCalled = false;

    #[\Override]
    public function getRepository(string $className): EntityRepository
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getCache(): Cache|null
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getConnection(): Connection
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getMetadataFactory(): ClassMetadataFactory
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getExpressionBuilder(): Expr
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function beginTransaction(): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function wrapInTransaction(callable $func): mixed
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function commit(): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function rollback(): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function createQuery(string $dql = ''): Query
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function createNativeQuery(string $sql, ResultSetMapping $rsm): NativeQuery
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function createQueryBuilder(): QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function find(string $className, mixed $id, int|LockMode|null $lockMode = null, ?int $lockVersion = null): object|null
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function refresh(object $object, int|LockMode|null $lockMode = null): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getReference(string $entityName, mixed $id): object|null
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function close(): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function lock(object $entity, int|LockMode $lockMode, \DateTimeInterface|int|null $lockVersion = null): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getEventManager(): EventManager
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getConfiguration(): Configuration
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function isOpen(): bool
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getUnitOfWork(): UnitOfWork
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function newHydrator(int|string $hydrationMode): AbstractHydrator
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getProxyFactory(): ProxyFactory
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getFilters(): FilterCollection
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function isFiltersStateClean(): bool
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function hasFilters(): bool
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function getClassMetadata(string $className): Mapping\ClassMetadata
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    /**
     * @throws \Exception
     */
    #[\Override]
    public function persist(object $object): void
    {
        $this->inputPersistedObject[] = $object;
        if ($this->throwPersistException instanceof \Exception) {
            throw $this->throwPersistException;
        }
    }

    #[\Override]
    public function remove(object $object): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function clear(): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function detach(object $object): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function flush(): void
    {
        $this->isFlushCalled = true;
    }

    #[\Override]
    public function initializeObject(object $obj): void
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function isUninitializedObject(mixed $value): bool
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }

    #[\Override]
    public function contains(object $object): bool
    {
        throw new \BadMethodCallException('Not implemented in EntityManagerMock.');
    }
}
