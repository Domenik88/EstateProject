<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListingController extends AbstractController
{
    private ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @Route("/listing/{listingId}-{feedName}", name="listing")
     */
    public function index(string $listingId, string $feedName)
    {
        $listingData = $this->listingService->getListingData($listingId,$feedName);
        return $this->render('listing/index.html.twig', [
            'controller_name' => 'ListingController',
            'listing' => $listingData,
        ]);
    }
}
