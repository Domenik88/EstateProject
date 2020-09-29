<?php

namespace App\Controller;

use App\Service\Listing\ListingMediaService;
use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends AbstractController
{
    private ListingService $listingService;
    private ListingMediaService $listingMediaService;

    public function __construct(ListingService $listingService, ListingMediaService $listingMediaService)
    {
        $this->listingService = $listingService;
        $this->listingMediaService = $listingMediaService;
    }

    /**
     * @Route("/listing/{listingId}-{feedName}", name="listing")
     */
    public function index(string $listingId, string $feedName)
    {
        $listing = $this->listingMediaService->getListingData($listingId,$feedName);
        return $this->render('listing/index.html.twig', [
            'controller_name' => 'ListingController',
            'listing' => $listing,
        ]);
    }
}
