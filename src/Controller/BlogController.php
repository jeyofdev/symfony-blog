<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Form\CommentType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogController extends AbstractController
{
    public function __construct (PostRepository $postRepository, CategoryRepository $categoryRepository, CommentRepository $commentRepository, ObjectManager $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
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
        $posts = $this->postRepository->findPublishedBy("created_at", "desc");
        
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
            $this->postRepository->findPublishedBy("created_at", "desc"),
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('blog/index.html.twig', [
            "posts" => $posts
        ]);
    }



    /**
     * @Route("/blog/category/{slug}-{id}", name="blog.category", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
     */
    public function category(Category $category, string $slug, PaginatorInterface $paginator, Request $request) : Response
    {
        // check if the slug of the current category exist
        if($category->getSlug() !== $slug){
            return $this->redirectToRoute("blog.category", [
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
     * @Route("/blog/user/{slug}-{id}", name="blog.user", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
     */
    public function user(User $user, string $slug, PaginatorInterface $paginator, Request $request) : Response
    {
        // check if the slug of the current category exist
        if($user->getSlug() !== $slug){
            return $this->redirectToRoute("blog.user", [
                "id" => $user->getId(),
                "slug" => $user->getSlug()
            ], 301);
        }

        /**
         * @var Post[];
         */
        $posts = $this->postRepository->findPostsByUser($user, "created_at", "desc");
        
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
            $this->postRepository->findPostsByUser($user, "created_at", "desc"),
            $request->query->getInt('page', 1),
            7
        );

        return $this->render('blog/user.html.twig', [
            "posts" => $posts,
            "currentUser" => $user
        ]);
    }



    /**
     * @Route("/blog/{slug}-{id}", name="blog.show", methods={"GET", "POST"}, requirements={"slug": "[a-z0-9\-]*", "id": "\d+"})
     */
    public function show (Post $post, string $slug, Request $request, ?UserInterface $user) : Response
    {
        // check if the slug of the current post exist
        if($post->getSlug() !== $slug){
            return $this->redirectToRoute("blog.show", [
                "id" => $post->getId(),
                "slug" => $post->getSlug()
            ], 301);
        }

        /**
         * @var Post[]
         */
        $lastPosts = $this->postRepository->findLast(10);

        // get the ids of the last posts
        $ids = [];
        foreach ($lastPosts as $item) {
            $ids[] = $item->getId();
        }

        // get a random posts
        $relatedPosts = [];
        for ($i = 0; $i < 3; $i++) { 
            $id = $ids[array_rand($ids)];
            $relatedPosts[] =  $this->postRepository->find(["id" => $id]);
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        // add a comment
        if ($form->isSubmitted() && $form->isValid()) {
            $timeZone = new DateTimeZone('Europe/Paris');
            $createdAt = new DateTime('now', $timeZone);

            $comment
                ->setCreatedAt($createdAt)
                ->setPost($post)
                ->setUser($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('blog.show', ['id' => $post->getId(), 'slug' => $post->getSlug()]);
        }

        return $this->render('blog/show.html.twig', [
            "post" => $post,
            "relatedPosts" => $relatedPosts,
            "form" => $form->createView()
        ]);
    }
}
