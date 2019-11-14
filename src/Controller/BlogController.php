<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index()
    {
        $posts = $this->repository->findAll();

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }
}
