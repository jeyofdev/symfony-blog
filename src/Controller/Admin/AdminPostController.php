<?php

namespace App\Controller\Admin;

use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminPostController extends AbstractController
{
    public function __construct (PostRepository $postRepository, ObjectManager $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/admin/post", name="admin.post.index", methods={"GET"}, )
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        // the paginated posts
        $posts = $paginator->paginate(
            $this->postRepository->findAllBy("created_at", "desc"),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
