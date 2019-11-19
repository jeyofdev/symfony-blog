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
        return $this->render('admin/home/index.html.twig', []);
    }
}
