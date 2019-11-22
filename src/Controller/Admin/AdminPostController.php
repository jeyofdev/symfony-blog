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
    public function __construct (PostRepository $repository, ObjectManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/admin/post", name="admin.post", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        // the paginated posts
        $posts = $paginator->paginate(
            $this->repository->findAllBy("created_at", "desc"),
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
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timeZone = new DateTimeZone('Europe/Paris');
            $createdAt = new DateTime('now', $timeZone);

            $post
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($createdAt)
                ->setSlug();

            $this->entityManager->persist($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'The post has been added');

            return $this->redirectToRoute('admin.post');
        }

        return $this->render('admin/post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/post/update/{id}", name="admin.post.update", methods={"GET", "POST"})
     */
    public function update (Post $post, Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

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
            $this->addFlash('success', 'The post has been updated');

            return $this->redirectToRoute('admin.post');
        }

        return $this->render('admin/post/update.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/post/delete/{id}", name="admin.post.delete", methods={"DELETE"})
     */
    public function delete (Post $post, Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        if($this->isCsrfTokenValid('delete' . $post->getId(), $request->get('_token'))) {
            $this->entityManager->remove($post);
            $this->entityManager->flush();
            $this->addFlash('success', 'The post has been deleted');
        } else {
            $this->addFlash('danger', 'You are not authorized to delete this post');
        }

        return $this->redirectToRoute('admin.post');
    }



    /**
     * @Route("/admin/post/publish/{id}", name="admin.post.publish", methods={"GET"})
     */
    public function publish (Post $post) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        $post->setPublished(1);
        $this->entityManager->persist($post);
        $this->entityManager->flush();

        $this->addFlash('success', 'The post has been published');

        return $this->redirectToRoute('admin.post');
    }
}
