<?php

namespace App\Repository;

use App\Entity\Advice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advice>
 *
 * @method Advice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advice[]    findAll()
 * @method Advice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advice::class);
    }

    public function add(Advice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Advice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Advice[] Returns an array of advices objects ordered by descending date with a limit of 5 by default
     */
    public function findForHome(int $limit = 5)
    {
        return $this->createQueryBuilder('ad')
            ->orderBy('ad.created_at', 'DESC')
            ->where('ad.status = 1')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Advice[] Returns an array of advices objects filtered by user, published or removed, and ordered by descending date
     */
    public function findAllByUser($author)
    {

        return $this->createQueryBuilder('ar')
            ->where('ar.author = :author')
            ->setParameter("author", $author)
            ->andWhere('ar.status = 1 OR ar.status = 2')
            ->orderBy('ar.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // Available parameters: category, page, limit, offset, sorttype, order, search
    public function findAllWithParameters(
        ?int $category,
        ?int $status,
        int $limit,
        int $offset,
        string $sortType,
        string $order,
        ?string $search
    ) {
        $qb = $this->createQueryBuilder('ad');

        if ($category) {
            $qb->andWhere('ad.category = :category')
                ->setParameter('category', $category);
        }

        if ($search) {
            $qb->andWhere('ad.content LIKE :search')->setParameter('search', "%$search%");
        }

        if ($status) {
            $qb->andWhere('ad.status = :status')->setParameter('status', $status);
        }

        $qb->orderBy('ad.' . $sortType, $order);
        $qb->setFirstResult($offset)->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Advice[] Returns an array of Advice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Advice
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
