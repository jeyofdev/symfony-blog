<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminHomeController extends AbstractController
{
    /**
     * @Route("/admin", name="admin", methods={"GET"})
     */
    public function index()
    {
        $this->denyAccessUnlessGranted(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], null, 'User tried to access a page without having ROLE_ADMIN or ROLE_SUPER_ADMIN');

        return $this->render('admin/home/index.html.twig', []);
    }
}
