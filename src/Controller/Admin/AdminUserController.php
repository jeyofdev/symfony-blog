<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    public function __construct (UserRepository $repository, ObjectManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/admin/user", name="admin.user", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        $this->denyAccessUnlessGranted(['ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_SUPER_ADMIN');
        
        // the paginated posts
        $users = $paginator->paginate(
            $this->repository->findAllBy("id", "desc"),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }



    /**
     * @Route("/admin/user/{id}/role", name="admin.user.edit.role", methods={"POST"})
     */
    public function editRole (User $user, Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_SUPER_ADMIN');

        if($this->isCsrfTokenValid('update' . $user->getId(), $request->get('_token'))) {
            $user->setRoles([$_POST['_role']]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'The role of the user has been changed successfully');
        } else {
            $this->addFlash('danger', 'You are not allowed to change the role of users');
        }

        return $this->redirectToRoute('admin.user');
    }
}
