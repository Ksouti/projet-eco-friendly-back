<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @return Users[] Returns an array of users objects filtered by users with ROLE_USER only ordered by descending date with a limit of 5 by default
     */
    public function findMembersForHome(int $limit = 5)
    {
        return $this->createQueryBuilder('u')
            ->where("u.roles NOT LIKE :roles")
            ->setParameter("roles", "%ROLE_AUTHOR%")
            ->andWhere("u.roles NOT LIKE :admin_roles")
            ->setParameter("admin_roles", "%ROLE_ADMIN%")
            ->addOrderBy('u.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Users[] Returns an array of users objects filtered by users with ROLE_AUTHOR ordered by descending date with a limit of 5 by default
     */
    public function findAuthorsForHome(int $limit = 5)
    {

        return $this->createQueryBuilder('u')
            ->where("u.roles LIKE :roles")
            ->setParameter("roles", "%ROLE_AUTHOR%")
            ->addOrderBy('u.created_at', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * 
     * @return Users[] Returns an array of users objects filtered by authors role ordered by descending date
     */
    public function listAllAuthors()
    {
        return $this->createQueryBuilder('u')
            ->where("u.roles LIKE :roles")
            ->setParameter("roles", "%ROLE_AUTHOR%")
            ->addOrderBy('u.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * 
     * @return Users[] Returns an array of users objects filtered by only user's role ordered by descending date
     */
    public function listAllMembers()
    {
        return $this->createQueryBuilder('u')
            ->where("u.roles NOT LIKE :roles")
            ->setParameter("roles", "%ROLE_AUTHOR%")
            ->andWhere("u.roles NOT LIKE :admin_roles")
            ->setParameter("admin_roles", "%ROLE_ADMIN%")
            ->addOrderBy('u.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
