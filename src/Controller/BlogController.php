<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    public function __construct (PostRepository $repository, ObjectManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/blog", name="blog")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        // the paginated posts
        $posts = $paginator->paginate(
            $this->repository->findAll(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }
}
