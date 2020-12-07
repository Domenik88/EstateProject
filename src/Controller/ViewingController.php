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
     * @Route("/viewing/new", name="new_viewing", methods={"POST"})
     */
    public function index(Request $request, ViewingService $viewingService)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new NotFoundHttpException();
        }

        $responseData = $viewingService->createViewing($request->request->get('formData'));
        $response = new JsonResponse();
        $response->setData(json_encode($responseData));
        return $response;
    }

}