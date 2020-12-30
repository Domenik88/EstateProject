<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminSeoController extends AbstractController
{
    /**
     * @Route("/admin/seo", priority=10, name="admin_seo")
     */
    public function index()
    {
        return $this->render('admin/admin_seo/index.html.twig', [
            'controller_name' => 'AdminSeoController',
        ]);
    }
}
