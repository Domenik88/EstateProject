<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use App\Service\Page\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ListingService $listingService;
    private PageService $pageService;

    public function __construct(ListingService $listingService, PageService $pageService)
    {
        $this->listingService = $listingService;
        $this->pageService = $pageService;
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

    /**
     * @Route ("/{slug}", name="page", requirements={"slug"=".+"})
     */
    public function page($slug)
    {
        $page = $this->pageService->search([ 'slug' => $slug, 'status' => true ]);
        if ( $page ) {
            return $this->render('page/' . $page->getType() . '-page.html.twig', [
                'page' => $page,
            ]);
        } else {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
    }

    /**
     * @Route("/contact-us", name="contact_us", priority=10)
     */
    public function contactUs()
    {
        $contactUsArr = [
            'phone'   => '778-918-5990',
            'address' => '247 Sixth St, New Westminster, BC V3L 3A5',
            'email'   => 'vadim@estateblock.com',
            'lat'     => 0,
            'lng'     => 0,
        ];
        return $this->render('contact_us/index.html.twig', [
            'contactsData' => $contactUsArr,
        ]);
    }

}
