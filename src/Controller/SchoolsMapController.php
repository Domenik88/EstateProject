<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 25.01.2021
 *
 * @package estateblock20
 */

namespace App\Controller;

use App\Service\School\SchoolService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SchoolsMapController extends AbstractController
{
    /**
     * @Route("/school/search", priority=10, name="school_search")
     */
    public function schoolSearch(Request $request, SchoolService $schoolService)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new NotFoundHttpException();
        }
        $boxObject = $request->request->get('box');
        $box = json_decode($boxObject);
        $schools = $schoolService->getAllSchoolsForMapBox($box->northEast->lat,$box->northEast->lng,$box->southWest->lat,$box->southWest->lng);
        $response = new JsonResponse(['collection' => json_encode($schools)]);
        $responseData = [];
        foreach ($schools as $school) {
            $responseData[] = $school->getDataForMap();
        }
        $response->setData($responseData);
        return $response;
    }

}