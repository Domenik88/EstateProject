<?php

namespace App\Controller;

use App\Service\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    private ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @Route("/listing/list", name="listing_list")
     */
    public function index()
    {
        $listingList = $this->listingService->getListingList('ddf');
        return $this->render('listing_list/index.html.twig', [
            'controller_name' => 'ListingListController',
            'listingList' => $listingList
        ]);
    }

    /**
     * @Route("/listing/list/{page}", name="listing_list_page")
     */
    public function show(int $page)
    {
        dump($page);
        die;
    }
}
