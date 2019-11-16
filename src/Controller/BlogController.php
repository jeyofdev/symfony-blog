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
        $posts = $this->postRepository->findAllBy("created_at", "desc");
        
        // add the categories associated with each posts
        foreach ($posts as $post) {
            /**
             * @var Category[]
             */
            $categories = $this->categoryRepository->findCategoriesByPost($post);
            $post->getCategories()->hydrateAdd($categories);
        }

        // the paginated posts
        $posts = $paginator->paginate(
            $this->postRepository->findAllBy("created_at", "desc"),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }



    /**
     * @Route("/blog/category/{id}", name="blog.category.index")
     */
    public function category(int $id, PaginatorInterface $paginator, Request $request) : Response
    {
        /**
         * @var Category
         */
        $currentCategory = $this->categoryRepository->find($id);
        
        /**
         * @var Post[];
         */
        $posts = $this->postRepository->findPostsByCategory($currentCategory, "created_at", "desc");
        
        // add the categories associated with each posts
        foreach ($posts as $post) {
            /**
             * @var Category[]
             */
            $categories = $this->categoryRepository->findCategoriesByPost($post);
            $post->getCategories()->hydrateAdd($categories);
        }

        // the paginated posts
        $posts = $paginator->paginate(
            $this->postRepository->findPostsByCategory($currentCategory, "created_at", "desc"),
            $request->query->getInt('page', 1),
            6
        );

        return $this->render('blog/category.html.twig', [
            "posts" => $posts,
            "currentCategory" => $currentCategory
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
