<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Form\NewAdminType;
use App\Repository\AdminRepository;
use App\Repository\ListingRepository;
use App\Service\Listing\ListingService;
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
    private ListingService $listingService;
    const LIMIT = 50;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

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
     * @Route("/listings/{page}", name="admin_listing_list", requirements={"page"="\d+"}, defaults={"title":"Preferences"})
     */
    public function preferences(Request $request, int $page = 1)
    {
        $offset = ($page - 1) * self::LIMIT;
        $adminListingListSearchResult = $this->listingService->getAdminListingList($page,self::LIMIT,$offset);
        return $this->render('admin/admin-listing-list.html.twig', [
            'controller_name' => 'AdminController',
            'listingList' => $adminListingListSearchResult,
            'ajaxPath' => '/listing/list/coordinates/',
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/listings/listing-{mlsId}", name="admin_listing_ajax", methods={"POST"})
     */
    public function setEstateblockListing(string $mlsId)
    {
        $response = $this->listingService->setAdminListingSelfListing($mlsId);
        return $this->json($response);
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
     * @Route("/{id}", name="admin_show", methods={"GET"}, defaults={"title":"Show user"}, requirements={"id"="\d+"})
     */
    public function show(Admin $admin, Request $request): Response
    {
        return $this->render('admin/user_list/admin_users/show.html.twig', [
            'user' => $admin,
            'title' =>  $request->attributes->get('title'),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET","POST"}, defaults={"title":"Edit user"}, requirements={"id"="\d+"})
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
     * @Route("/{id}", name="admin_delete", methods={"DELETE"}, defaults={"title":"Delete user"}, requirements={"id"="\d+"})
     */
    public function delete(Request $request, Admin $admin, AdminUserService $adminUserService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $adminUserService->removeAdminUser($admin);
        }

        return $this->redirectToRoute('admin_list');
    }
}
