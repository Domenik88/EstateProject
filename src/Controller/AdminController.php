<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin", defaults={"title":"Administrator panel"})
     */
    public function index(Request $request)
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/admin/profile", name="admin_profile", defaults={"title":"Administrator panel user profile"})
     */
    public function profile(Request $request)
    {
        return $this->render('admin/profile.html.twig', [
            'controller_name' => 'AdminController',
            'title' =>  $request->attributes->get('title'),
        ]);
    }
}
