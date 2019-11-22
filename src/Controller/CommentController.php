<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    public function __construct (PostRepository $postRepository, CommentRepository $commentRepository, ObjectManager $entityManager)
    {
        $this->postRepository = $postRepository;
        $this->commentRepository = $commentRepository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("blog/comment/delete/{id}", name="blog.comment.delete", methods={"GET", "DELETE"})
     */
    public function delete(Comment $comment, Request $request) : Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        $post = $this->postRepository->find($_POST['_postId']);

        if($this->isCsrfTokenValid('delete' . $comment->getId(), $request->get('_token'))) {
            $this->entityManager->remove($comment);
            $this->entityManager->flush();
            $this->addFlash('success', 'The comment has been deleted');
        } else {
            $this->addFlash('danger', 'You are not authorized to delete this comment');
        }

        return $this->redirectToRoute('blog.show', ['id' => $post->getId(), 'slug' => $post->getSlug()]);
    }
}
