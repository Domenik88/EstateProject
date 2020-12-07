<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.12.2020
 *
 * @package estateblock20
 */

namespace App\Controller;

use App\Service\Viewing\ViewingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ViewingController extends AbstractController
{
    /**
     * @Route("/viewing/new", name="new_viewing", methods={"GET","POST"})
     */
    public function index(Request $request, ViewingService $viewingService)
    {
        $arr = json_encode([
            'name' => 'Fabien',
            'phone' => '+71234567890',
            'email' => 'user@example.com',
            'listingId' => '225723441',
        ]);

        $request = Request::create(
            '/viewing/new',
            'POST',
            ['data' => $arr],
        );
//        if(!$request->isXmlHttpRequest())
//        {
//            throw new NotFoundHttpException();
//        }

        $responseData = $viewingService->createViewing($request);
        $response = new JsonResponse();
        $response->setData($responseData);
        return $response;
    }

}