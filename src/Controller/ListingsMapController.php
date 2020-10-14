<?php

namespace App\Controller;

use App\Service\Listing\ListingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $boxObject = $request->request->get('box');
        $box = json_decode($boxObject);
        $boxString = "box '((" . $box->northEast->lat . ", ". $box->northEast->lng . "),(" . $box->southWest->lat . ", " . $box->southWest->lng . "))'";
        $listings = $listingService->getAllActiveListingsForMapBox($boxString);
        $response = new JsonResponse(['collection' => json_encode($listings)]);
//        $response->setData([
//            'data' => 123,
//        ]);
//        $response->setContent(json_encode($listings));
        foreach ($listings as $listing) {
            dump($listing->getCoordinates());
            die;
        }
//        dump($listings);
//        dump($response);
        die;
        return $response;
    }

}
