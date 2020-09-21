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
     * @Route("/listing/{slug}", name="listing_list")
     */
    public function index(string $slug)
    {
        dump($slug);
        die;
//        $listingList = $this->listingService->getListingList('ddf');
        return $this->render('listing_list/index.html.twig', [
            'controller_name' => 'ListingListController',
//            'listingList' => $listingList
        ]);
    }

    /**
     * @Route("/listing/{page}", name="listing_list")
     */
    public function show(int $page)
    {
        dump($page);
        die;
    }
}
