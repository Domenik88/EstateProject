<?php

namespace App\Controller;

use App\Service\Listing\ListingSearchDataService;
use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingsMapController extends AbstractController
{
    /**
     * @Route("/map", priority=10, name="listings_map")
     */
    public function index()
    {
        return $this->render('listings_map/index.html.twig', [
            'controller_name' => 'ListingsMapController',
        ]);
    }

    /**
     * @Route("/listing/search", priority=10, name="listings_search")
     */
    public function listingSearch(Request $request, ListingService $listingService, ListingSearchDataService $listingSearchDataService)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new NotFoundHttpException();
        }
        $boxObject = $request->request->get('box');
        $box = json_decode($boxObject);
        $listings = $listingService->getAllActiveListingsForMapBox($box->northEast->lat,$box->northEast->lng,$box->southWest->lat,$box->southWest->lng);
        $response = new JsonResponse(['collection' => json_encode($listings)]);
        $responseData = [];
        foreach ($listings as $listing) {
            $responseData[] = $listingSearchDataService->constructSearchListingData($listing);
        }
        $response->setData($responseData);
        return $response;
    }


}
