<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use App\Service\Listing\ListingSimilarSearch;
use App\Service\Viewing\ViewingRequestData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("/listing/{province}/{mlsNum}-{feedName}", name="listing")
     */
    public function index(string $province, string $mlsNum, string $feedName)
    {
        $listingData = $this->listingService->getListingData($province,$mlsNum,$feedName);
        $similarListings = $this->listingSimilarSearch->getSimilarListingsData($listingData);
        return $this->render('listing/index.html.twig', [
            'controller_name' => 'ListingController',
            'listing' => $listingData,
            'similarListings' => $similarListings,
        ]);
    }

}
