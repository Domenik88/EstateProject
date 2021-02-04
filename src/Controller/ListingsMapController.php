<?php

namespace App\Controller;

use App\Criteria\ListingMapSearchCriteria;
use App\Service\Listing\ListingSearchDataService;
use App\Service\Listing\ListingSearchService;
use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingsMapController extends AbstractController
{
    private ListingService $listingService;
    private ListingSearchService $listingSearchService;

    public function __construct(ListingService $listingService, ListingSearchService $listingSearchService)
    {
        $this->listingService = $listingService;
        $this->listingSearchService = $listingSearchService;
    }

    /**
     * @Route("/map", priority=10, name="listings_map")
     */
    public function index()
    {
        $searchFormObject = $this->listingService->getSearchFormObject();
        return $this->render('listings_map/index.html.twig', [
            'searchFormObject' => $searchFormObject,
        ]);
    }

    /**
     * @Route("/listing/search", priority=10, name="listings_search")
     */
    public function listingSearch(Request $request,
                                  ListingService $listingService,
                                  ListingSearchDataService $listingSearchDataService)
    {
        if ( !$request->isXmlHttpRequest() ) {
            throw new NotFoundHttpException();
        }
        $boxObject = $request->request->get('box');
        $box = json_decode($boxObject);
        $listings = $listingService->getAllActiveListingsForMapBox($box->northEast->lat, $box->northEast->lng,
                                                                   $box->southWest->lat, $box->southWest->lng);
        $response = new JsonResponse([ 'collection' => json_encode($listings) ]);
        $responseData = [];
        foreach ( $listings as $listing ) {
            $responseData[] = $listingSearchDataService->constructSearchListingData($listing);
        }
        $response->setData($responseData);
        return $response;
    }

    /**
     * @Route("/map/{city},{state}/", priority=10, name="search_on_map", requirements={"city"=".+","state"=".+"})
     */
    public function searchOnMap(string $city, string $state)
    {
        $criteria = new ListingMapSearchCriteria($city, $this->listingService->getProvinceName($state));
        $searchListings = $this->listingSearchService->searchListings($criteria);
        $searchFormObject = $this->listingService->getSearchFormObject();
        return $this->render('listings_map/index.html.twig', [
            'searchListings'   => $searchListings,
            'searchFormObject' => $searchFormObject,
        ]);
    }

}
