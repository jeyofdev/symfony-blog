<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use \DateTime;
use \DateTimeZone;
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
     * @Route("/admin/post", name="admin.post.index", methods={"GET"})
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



    /**
     * @Route("/admin/post/new", name="admin.post.new", methods={"GET", "POST"})
     */
    public function new (Request $request) : response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timeZone = new DateTimeZone('Europe/Paris');
            $createdAt = new DateTime('now', $timeZone);

            $post
                ->setCreatedAt($createdAt)
                ->setSlug();

            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your post has been added');

            return $this->redirectToRoute('admin.post.index');
        }

        return $this->render('admin/post/new.html.twig', [
            'posts' => $post,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/post/update/{id}", name="admin.post.update", methods={"GET", "POST"})
     */
    public function update (Post $post, Request $request) : response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timeZone = new DateTimeZone('Europe/Paris');
            $updatedAt = new DateTime('now', $timeZone);

            $post
                ->setUpdatedAt($updatedAt)
                ->setSlug();

            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your post has been updated');

            return $this->redirectToRoute('admin.post.index');
        }

        return $this->render('admin/post/update.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
