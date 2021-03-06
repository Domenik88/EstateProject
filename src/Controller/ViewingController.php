<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 04.12.2020
 *
 * @package estateblock20
 */

namespace App\Controller;

use App\Service\Viewing\ViewingRequestData;
use App\Service\Viewing\ViewingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ViewingController extends AbstractController
{
    /**
     * @Route("/viewing/new", priority=10, name="new_viewing", methods={"POST"})
     */
    public function index(Request $request, ViewingService $viewingService): Response
    {
        if ( !$request->isXmlHttpRequest() ) {
            throw new NotFoundHttpException();
        }
        $formData = json_decode($request->request->get('formData'));
        $responseData = $viewingService->createViewing(new ViewingRequestData(
            $formData->name->value,
            $formData->email->value,
            $formData->code->value.$formData->phone->value,
            $formData->listingId->value
        ));
        return $this->json($responseData, $responseData->statusCode);
    }

}