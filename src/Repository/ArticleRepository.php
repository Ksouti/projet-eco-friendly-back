<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
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

    /**
     * @return Article[] Returns an array of articles objects ordered by descending date with a limit of 5 by default
     */
    public function findForHome(int $limit = 5)
    {
        return $this->createQueryBuilder('ar')
            ->orderBy('ar.created_at', 'DESC')
            ->where('ar.status = 1')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of articles objects ordered by descending date
     */
    public function findAllOrderByDate()
    {
        return $this->createQueryBuilder('ar')
            ->orderBy('ar.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Article[] Returns an array of articles objects filtered by user
     */
    public function findAllByUser($author)
    {

        return $this->createQueryBuilder('ar')
            ->where("ar.author = :author")
            ->setParameter("author", $author)
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
        $qb = $this->createQueryBuilder('ar');

        if ($category) {
            $qb->andWhere('ar.category = :category')
                ->setParameter('category', $category);
        }

        if ($search) {
            $qb->andWhere('ar.content LIKE :search')->setParameter('search', "%$search%");
        }

        if ($status) {
            $qb->andWhere('ar.status = :status')->setParameter('status', $status);
        }

        $qb->orderBy('ar.' . $sortType, $order);
        $qb->setFirstResult($offset)->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

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
