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
     * @Route("/", name="home")
     */
    public function index()
    {
        $selfListings = $this->listingService->getSelfListingsForHomepage();
        $cityCounters = $this->listingService->getCitiesCounters();
        return $this->render('default/index.html.twig', [
            'selfListings' => $selfListings,
            'cityCounters' => $cityCounters,
            'landingPageRouteName' => '',
        ]);
    }
}
