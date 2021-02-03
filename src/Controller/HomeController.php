<?php

namespace App\Controller;

use App\Service\Listing\ListingSearchService;
use App\Service\Listing\ListingService;
use App\Service\Page\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private ListingService $listingService;
    private PageService $pageService;

    public function __construct(ListingService $listingService,
                                PageService $pageService)
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
        }
        else {
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

    /**
     * @Route ("/how-it-works", name="how-it-works", priority=10)
     */
    public function howItWorks()
    {
        return $this->render('how-it-works/index.html.twig');
    }

    /**
     * @Route ("/selling", name="selling", priority=10)
     */
    public function selling()
    {
        return $this->render('selling/index.html.twig');
    }

    /**
     * @Route ("/buying", name="buying", priority=10)
     */
    public function buying()
    {
        return $this->render('buying/index.html.twig');
    }

    /**
     * @Route ("/sitemap", name="sitemap", priority=10)
     */
    public function sitemap()
    {
        return $this->render('sitemap/index.html.twig');
    }

    /**
     * @Route ("/browse-by-street", name="browse-by-street", priority=10)
     */
    public function browseByStreet()
    {
        return $this->render('browse_by_street/index.html.twig');
    }

    /**
     * @Route ("/assessment", name="assessment", priority=10)
     */
    public function assessment()
    {
        return $this->render('assessment/index.html.twig');
    }

    /**
     * @Route ("/price-your-home", name="price-your-home", priority=10)
     */
    public function priceYourHome()
    {
        return $this->render('price_your_home/index.html.twig');
    }

}
