<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
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
     * @Route("/blog", name="blog", methods={"GET"})
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
            7
        );

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }



    /**
     * @Route("/blog/category/{slug}-{id}", name="blog.category.index", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
     */
    public function category(Category $category, string $slug, PaginatorInterface $paginator, Request $request) : Response
    {
        // check if the slug of the current category exist
        if($category->getSlug() !== $slug){
            return $this->redirectToRoute("blog.category.index", [
                "id" => $category->getId(),
                "slug" => $category->getSlug()
            ], 301);
        }

        /**
         * @var Post[];
         */
        $posts = $this->postRepository->findPostsByCategory($category, "created_at", "desc");
        
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
            $this->postRepository->findPostsByCategory($category, "created_at", "desc"),
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('blog/category.html.twig', [
            "posts" => $posts,
            "currentCategory" => $category
        ]);
    }



    /**
     * @Route("/blog/{slug}-{id}", name="blog.show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
     */
    public function show (Post $post, string $slug) : Response
    {
        // check if the slug of the current post exist
        if($post->getSlug() !== $slug){
            return $this->redirectToRoute("blog.show", [
                "id" => $post->getId(),
                "slug" => $post->getSlug()
            ], 301);
        }

        return $this->render('blog/show.html.twig', [
            "post" => $post
        ]);
    }
}
