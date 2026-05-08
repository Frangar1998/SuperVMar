<?php

namespace SuperVMar\Authentication\Infrastructure\Symfony;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final readonly class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(private Connection $connection) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $userData = $this->connection->createQueryBuilder()
            ->select('id', 'username', 'password', 'isAdmin')
            ->from('user')
            ->where('username = :username')
            ->setParameter('username', $identifier)
            ->executeQuery()
            ->fetchAssociative();

        if (!$userData) {
            throw new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
        }

        $job = $this->searchJobName($userData['id']);

        return new SecurityUser(
            $userData['id'],
            $userData['username'],
            $userData['password'],
            (int) $userData['isAdmin'],
            $job,
        );
    }

    private function searchJobName(string $userId): ?string
    {
        $result = $this->connection->createQueryBuilder()
            ->select('j.name')
            ->from('worker_allocation', 'wa')
            ->join('wa', 'job', 'j', 'j.id = wa.idJob')
            ->where('wa.idUser = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery()
            ->fetchOne();

        return $result ?: null;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return SecurityUser::class === $class;
    }
}
