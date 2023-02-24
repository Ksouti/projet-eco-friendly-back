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
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD

<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
    // public function findAllByUser(Article $entity, bool $flush = false): void
    // {
    //     $this->getEntityManager()->remove($entity);

    //     if ($flush) {
    //         $this->getEntityManager()->flush();
    //     }
    // }

=======
>>>>>>> FEAT: list authors et list members
    /**
     * 
     * @return Article[] Returns an array of articles objects ordered by userId
     */
>>>>>>> articles by an author
    public function findAllOrderByUserId($author){
        
        return $this->createQueryBuilder('ar')
            ->where("ar.author = :author")
            ->setParameter("author",$author)
=======
=======
>>>>>>> WIP: api lists param
    public function findAllWithParameters(?int $offset, ?int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
<<<<<<< HEAD
>>>>>>> WIP: api lists param
            ->getQuery()
=======
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
<<<<<<< HEAD
    /* $qb = $this->createQueryBuilder('ar')
=======
    // Available parameters: category, page, limit, offset, sorttype, order, search
    public function findAllWithParameters(array $needles): array
    {
>>>>>>> FEAT: Api ArticleController working
        if (empty($needles)) {
            // Returns all articles without any parameters
            return $this->createQueryBuilder('ar')
                ->getQuery()
                ->getResult();
        }

<<<<<<< HEAD
=======
        $qb = $this->createQueryBuilder('ar')
>>>>>>> FEAT: Api ArticleController working
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
<<<<<<< HEAD
            $qb->andWhere('ar.content LIKE :search')
                ->setParameter('search', "%" . $needles['search'] . "%");
        }

        return $qb->getQuery()
>>>>>>> FEAT: Api ArticleController working
            ->getResult();
    } */
=======
>>>>>>> FEAT: Api Articles list with parameters

<<<<<<< HEAD
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

=======
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

=======
            ->getQuery()
=======
            $qb->andWhere('a.content LIKE :search')
                ->setParameter('search', '%' . $needles['search'] . '%');
        }

        return $qb->getQuery()
>>>>>>> FEAT: Api ArticleController working
            ->getResult();
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

>>>>>>> WIP: api lists param
=======
    
    /**
     * @return Article[] Returns an array of articles objects ordered by userId
     */
    public function findAllOrderByUserId($author){
        
        return $this->createQueryBuilder('ar')
            ->where("ar.author = :author")
            ->setParameter("author",$author)
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

>>>>>>> c3b7c9e2b232ba2b63e327a802af86ad5c732c78
    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
<<<<<<< HEAD
<<<<<<< HEAD
>>>>>>> WIP: api lists param
=======
>>>>>>> WIP: api lists param
=======
>>>>>>> c3b7c9e2b232ba2b63e327a802af86ad5c732c78
}
