<?php

namespace App\Controller;

use App\Service\Listing\ListingSearchService;
use App\Service\Listing\ListingService;
use App\Service\Page\PageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private PageService $pageService;
    private ListingService $listingService;
    private ListingSearchService $listingSearchService;

    public function __construct(PageService $pageService, ListingService $listingService, ListingSearchService $listingSearchService)
    {
        $this->pageService = $pageService;
        $this->listingService = $listingService;
        $this->listingSearchService = $listingSearchService;
    }

    /**
     * @Route ("/{slug}", name="page", requirements={"slug"=".+"}, methods={"GET"})
     */
    public function page($slug)
    {
        $page = $this->pageService->search([ 'slug' => $slug, 'status' => true ]);
        if ( $page ) {
            if ($page->getType() != 'search') {
                return $this->render('page/' . $page->getType() . '-page.html.twig', [
                    'page' => $page,
                ]);
            } else {
                $searchFormObject = $this->listingService->getSearchFormObject();
                $searchListings = $this->listingSearchService->getListingsForSearchPage($page);
                return $this->render('listings_map/index.html.twig', [
                    'searchListings'   => $searchListings,
                    'searchFormObject' => $searchFormObject,
                ]);
            }
        } else {
            return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
        }
    }

}
