<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }


    /**
     * @return Post[] Returns an array of all Category objects
     */
    public function findAllBy(string $orderBy, string $order = 'asc')
    {
        return $this->createQueryBuilder('c')
            ->orderBy("c.$orderBy", $order)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Category[] Returns an array of each Category object that corresponds to a post object
     */
    public function findCategoriesByPost(Post $post)
    {
        return $this->createQueryBuilder('c')
            ->join('c.posts', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $post->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Category[] Returns an array of Category objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Category
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
