<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BlogController extends AbstractController
{
    public function __construct (PostRepository $postRepository, CategoryRepository $categoryRepository, ObjectManager $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/blog", name="blog.index")
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        /**
         * @var Post[];
         */
        $posts = $this->postRepository->findAllByDate("desc");
        
        // add categories associated with each posts
        foreach ($posts as $post) {
            $categories = $this->categoryRepository->findCategoriesByPost($post);
            $post->getCategories()->hydrateAdd($categories);
        }

        // the paginated posts
        $posts = $paginator->paginate(
            $this->postRepository->findAllByDate("desc"),
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
        $post = $this->postRepository->find($id);

        return $this->render('blog/show.html.twig', [
            "post" => $post
        ]);
    }
}
