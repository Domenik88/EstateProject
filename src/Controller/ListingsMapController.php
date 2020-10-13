<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/map/ajax", name="listings_map_ajax")
     */
    public function mapAjax()
    {
//        $isAjax = $this->get('Request')->isXMLHttpRequest();
        dump($this);
        dump($this->get('session'));
        die;
//        if ($isAjax) {
//            ...
//            return new JsonResponse([['mlsNum'=>'R123456789','address'=>'У черта на куличках','lat'=>0,'lng'=>0],['mlsNum'=>'R123456789','address'=>'У черта на куличках','lat'=>10,'lng'=>10]]);
//        }
        return new JsonResponse([['mlsNum'=>'R123456789','address'=>'У','lat'=>0,'lng'=>0],['mlsNum'=>'R123456789','address'=>'У','lat'=>10,'lng'=>10]]);
    }

}
