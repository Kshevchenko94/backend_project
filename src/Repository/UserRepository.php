<?php

namespace App\Repository;

use App\Entity\User;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    const DEFAULT_IS_ACTIVATED = false;
    const DEFAULT_IS_BLOCKED = false;
    const DEFAULT_IS_PUBLIC = false;
    const DEFAULT_RATE = 0.0;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @throws ORMException
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function createUser(array $data, UserPasswordHasherInterface $passwordHashed): bool
    {
        if (count($data)) {
            $username = $data['username'] ?? null;
            $password = $data['password'] ?? null;
            $lastName = $data['last_name'] ?? null;
            $firstName = $data['first_name'] ?? null;
            $sex = $data['sex'] ?? null;
            $user = new User();
            $hashedPassword = $passwordHashed->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);
            $user->setUsername($username);
            $user->setLastName($lastName);
            $user->setFirstName($firstName);
            $user->setIsActivated(self::DEFAULT_IS_ACTIVATED);
            $user->setIsBlock(self::DEFAULT_IS_BLOCKED);
            $user->setIsPublic(self::DEFAULT_IS_PUBLIC);
            $user->setRate(self::DEFAULT_RATE);
            $user->setSex($sex);
            $user->setDateCreated(time());
            $this->_em->persist($user);
            $this->_em->flush();
            return true;
        }
        return false;
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */


    /**
     * @param string $username
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function findOneByUsername(string $username): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
