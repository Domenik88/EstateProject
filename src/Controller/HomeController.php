<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @Route("/", name="home", priority=10)
     */
    public function index()
    {
        $selfListings = $this->listingService->getSelfListingsForHomepage();
        $cityCounters = $this->listingService->getCitiesCounters();
        $featuredProperties = $this->listingService->getFeaturedProperties();
        $searchFormObject = $this->listingService->getSearchFormObject();
        return $this->render('default/index.html.twig', [
            'selfListings'         => $selfListings,
            'featuredProperties'   => $featuredProperties,
            'cityCounters'         => $cityCounters,
            'landingPageRouteName' => '',
            'searchFormObject'     => $searchFormObject,
        ]);
    }

}
