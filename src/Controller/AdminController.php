<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Form\NewAdminType;
use App\Repository\AdminRepository;
use App\Service\User\AdminUserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin", defaults={"title":"Administrator panel"})
     */
    public function index(Request $request)
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/profile", name="admin_profile", defaults={"title":"Administrator panel user profile"})
     */
    public function profile(Request $request)
    {
        return $this->render('admin/profile.html.twig', [
            'controller_name' => 'AdminController',
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/list/admin", name="admin_list", defaults={"title":"Administrator users list"})
     */
    public function adminList(AdminRepository $adminRepository, Request $request): Response
    {
        return $this->render('admin/user_list/admin_users/index.html.twig', [
            'users' => $adminRepository->findAll(),
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"GET","POST"}, defaults={"title":"Create new Admin user"})
     */
    public function new(Request $request, AdminUserService $adminUserService): Response
    {
        $user = new Admin();
        $form = $this->createForm(NewAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUserService->createNewAdminUser($user);

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/user_list/admin_users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_show", methods={"GET"}, defaults={"title":"Show user"})
     */
    public function show(Admin $admin, Request $request): Response
    {
        return $this->render('admin/user_list/admin_users/show.html.twig', [
            'user' => $admin,
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET","POST"}, defaults={"title":"Edit user"})
     */
    public function edit(Request $request, Admin $admin, AdminUserService $adminUserService): Response
    {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminUserService->editAdminUser();

            return $this->redirectToRoute('admin_list');
        }

        return $this->render('admin/user_list/admin_users/edit.html.twig', [
            'user' => $admin,
            'form' => $form->createView(),
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_delete", methods={"DELETE"}, defaults={"title":"Delete user"})
     */
    public function delete(Request $request, Admin $admin, AdminUserService $adminUserService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $adminUserService->removeAdminUser($admin);
        }

        return $this->redirectToRoute('admin_list');
    }
}
