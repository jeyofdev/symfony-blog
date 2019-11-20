<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{
    public function __construct (CategoryRepository $repository, ObjectManager $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/admin/category", name="admin.category.index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request) : Response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        // the paginated categories
        $categories = $paginator->paginate(
            $this->repository->findAllBy("id", "desc"),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }



    /**
     * @Route("/admin/category/new", name="admin.category.new", methods={"GET", "POST"})
     */
    public function new (Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug();

            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'The category has been added');

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/category/update/{id}", name="admin.category.update", methods={"GET", "POST"})
     */
    public function update (Category $category, Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug();

            $this->entityManager->persist($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'The category has been updated');

            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/update.html.twig', [
            'category' => $category,
            'form' => $form->createView()
        ]);
    }



    /**
     * @Route("/admin/category/delete/{id}", name="admin.category.delete", methods={"GET", "DELETE"})
     */
    public function delete (Category $category, Request $request) : response
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        if($this->isCsrfTokenValid('delete' . $category->getId(), $request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();
            $this->addFlash('success', 'The category has been deleted');
        } else {
            $this->addFlash('danger', 'You are not authorized to delete this category');
        }

        return $this->redirectToRoute('admin.category.index');
    }
}
