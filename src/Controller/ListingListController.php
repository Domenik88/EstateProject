<?php

namespace App\Controller;

use App\Service\Listing\ListingListSinglePageListingsCoordinates;
use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    private ListingService $listingService;
    private ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates;
    const LIMIT = 50;

    public function __construct(ListingService $listingService, ListingListSinglePageListingsCoordinates $listingListSinglePageListingsCoordinates)
    {
        $this->listingService = $listingService;
        $this->listingListSinglePageListingsCoordinates = $listingListSinglePageListingsCoordinates;
    }

    /**
     * @Route("/listing/list/{page}", priority=10, name="listing_list", requirements={"page"="\d+"})
     */
    public function list(int $page = 1)
    {
        $offset = ($page - 1) * self::LIMIT;
        $listingListSearchResult = $this->listingService->getListingList('ddf',$page,self::LIMIT,$offset);
        return $this->render('listing_list/index.html.twig', [
            'controller_name' => 'ListingListController',
            'listingList' => $listingListSearchResult,
            'ajaxPath' => '/listing/list/coordinates/'
        ]);
    }

    /**
     * @Route("/listing/list/coordinates/{page}", priority=10, name="listing_list_coordinates", requirements={"page"="\d+"})
     */
    public function listCoordinates(int $page = 1): ?JsonResponse
    {
        $offset = ($page - 1) * self::LIMIT;
        $listingListCoordinates = $this->listingService->getListingListCoordinates('ddf',$page,self::LIMIT,$offset);
        return new JsonResponse($listingListCoordinates);
    }
}
