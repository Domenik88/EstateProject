<?php

namespace App\Controller;

use App\Service\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    private ListingService $listingService;
    const LIMIT = 50;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @Route("/listing/list/{page}", name="listing_list", requirements={"page"="\d+"})
     */
    public function list(int $page = 1)
    {
        $offset = ($page - 1) * self::LIMIT;
        $listingListSearchResult = $this->listingService->getListingList('ddf',$page,self::LIMIT,$offset);
        return $this->render('listing_list/index.html.twig', [
            'controller_name' => 'ListingListController',
            'listingList' => $listingListSearchResult,
        ]);
    }
}
