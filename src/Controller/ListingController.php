<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use App\Service\Listing\ListingSimilarSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends AbstractController
{
    private ListingService $listingService;
    private ListingSimilarSearch $listingSimilarSearch;

    public function __construct(ListingService $listingService, ListingSimilarSearch $listingSimilarSearch)
    {
        $this->listingService = $listingService;
        $this->listingSimilarSearch = $listingSimilarSearch;
    }

    /**
     * @Route("/listing/{province}/{listingId}-{feedName}", name="listing")
     */
    public function index(string $province, string $listingId, string $feedName)
    {
        $listingData = $this->listingService->getListingData($province,$listingId,$feedName);
        $similarListings = $this->listingSimilarSearch->getSimilarListingsData($listingData);
        return $this->render('listing/index.html.twig', [
            'controller_name' => 'ListingController',
            'listing' => $listingData,
            'similarListings' => $similarListings,
        ]);
    }
}
