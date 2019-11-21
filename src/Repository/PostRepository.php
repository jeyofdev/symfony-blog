<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }


    /**
     * @return Post[] Returns an array of the latest Post objects
     */
    public function findLast(int $limit)
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.id", 'desc')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Post[] Returns an array of all Post objects
     */
    public function findAllBy(string $orderBy, string $order = 'asc')
    {
        return $this->createQueryBuilder('p')
            ->orderBy("p.$orderBy", $order)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Post[] Returns an array of each Post object that corresponds to a Category object
     */
    public function findPostsByCategory(Category $category, string $orderBy, string $order = 'asc')
    {
        return $this->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $category->getId())
            ->orderBy("p.$orderBy", $order)
            ->getQuery()
            ->getResult()
        ;
    }


    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
