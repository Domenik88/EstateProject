<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        $selfListings = $this->listingService->getSelfListingsForHomepage();
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'selfListings' => $selfListings,
        ]);
    }
}
