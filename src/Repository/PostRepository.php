<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
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
     * @return Post[] Returns an array of all Post published objects
     */
    public function findPublishedBy(string $orderBy, string $order = 'asc')
    {
        return $this->createQueryBuilder('p')
            ->where('p.published = 1')
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
            ->andWhere('c.id = :id')
            ->andWhere('p.published = 1')
            ->setParameter('id', $category->getId())
            ->orderBy("p.$orderBy", $order)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return Post[] Returns an array of each Post object that corresponds to a User object
     */
    public function findPostsByUser(User $user, string $orderBy, string $order = 'asc')
    {
        return $this->createQueryBuilder('p')
            ->join('p.user', 'u')
            ->andWhere('u.id = :id')
            ->andWhere('p.published = 1')
            ->setParameter('id', $user->getId())
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
