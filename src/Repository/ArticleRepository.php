<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    // Available parameters: category, page, limit, offset, sorttype, order, search
    public function findAllWithParameters(
        $category = null,
        $limit = 10,
        $offset = 0,
        $sortType = 'created_at',
        $order = 'desc',
        $search = null,
        $status = null
    ) {
        $qb = $this->createQueryBuilder('ar');

        if ($category) {
            $qb->andWhere('ar.category = :category')->setParameter('category', $category);
        }

        if ($search) {
            $qb->andWhere('ar.name LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        if ($status) {
            $qb->andWhere('ar.status = :status')->setParameter('status', $status);
        }

        $qb->orderBy('ar.' . $sortType, $order);
        $qb->setFirstResult($offset)->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
    /* $qb = $this->createQueryBuilder('ar')
        if (empty($needles)) {
            // Returns all articles without any parameters
            return $this->createQueryBuilder('ar')
                ->getQuery()
                ->getResult();
        }

            ->join('ar.category', 'c')
            ->orderBy('ar.' . ($needles['sorttype'] ?? 'created_at'), $needles['order'] ?? 'DESC')
            ->where('ar.category = :category')
            ->setParameter('category', $needles['category'] ?? null);

        if (isset($needles['page'])) {
            $qb->setFirstResult($needles['page'] * 10 - 9)
                ->setMaxResults(10);
        }

        if (isset($needles['offset'])) {
            $qb->setFirstResult($needles['offset']);
        }

        if (isset($needles['limit'])) {
            $qb->setMaxResults($needles['limit']);
        }

        if (isset($needles['search'])) {
            $qb->andWhere('ar.content LIKE :search')
                ->setParameter('search', "%" . $needles['search'] . "%");
        }

        return $qb->getQuery()
            ->getResult();
    } */

    //    /**
    //     * @return Article[] Returns an array of Article objects
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

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
