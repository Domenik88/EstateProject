<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ListingsMapController extends AbstractController
{
    /**
     * @Route("/map", name="listings_map")
     */
    public function index()
    {
        return $this->render('listings_map/index.html.twig', [
            'controller_name' => 'ListingsMapController',
        ]);
    }

    /**
     * @Route("/listing/search", name="listings_search")
     */
    public function listingSearch(Request $request, ListingService $listingService)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new NotFoundHttpException();
        }
        $box = $request->request->get('box');
        $listings = $listingService->getAllActiveListingsForMapBox($box);
        dump($box);
        dump(json_decode($box));
        dump($listings);
        die;
        return new JsonResponse([['mlsNum'=>'R123456789','address'=>'У','lat'=>0,'lng'=>0],['mlsNum'=>'R123456789','address'=>'У','lat'=>10,'lng'=>10]]);
    }

}
