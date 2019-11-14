<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    public function __construct (PostRepository $repository, ObjectManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/blog", name="blog.index")
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        // the paginated posts
        $posts = $paginator->paginate(
            $this->repository->findAllBy(),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }



    /**
     * @Route("/blog/{id}", name="blog.show")
     */
    public function show (int $id) : Response
    {
        // the current post
        $post = $this->repository->find($id);

        return $this->render('blog/show.html.twig', [
            "post" => $post
        ]);
    }
}
