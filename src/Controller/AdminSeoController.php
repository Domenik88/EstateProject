<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminSeoController extends AbstractController
{
    /**
     * @Route("/admin/seo", name="admin_seo")
     */
    public function index()
    {
        return $this->render('admin/admin_seo/index.html.twig', [
            'controller_name' => 'AdminSeoController',
        ]);
    }
}
