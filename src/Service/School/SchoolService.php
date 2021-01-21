<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 14.12.2020
 *
 * @package estateblock20
 */

namespace App\Service\School;

use App\Entity\School;
use App\Repository\SchoolRepository;
use App\Service\Geo\Point;
use App\Service\Geo\Polygon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class SchoolService
{
    private EntityManagerInterface $entityManager;
    private SchoolRepository $schoolRepository;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, SchoolRepository $schoolRepository, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->schoolRepository = $schoolRepository;
        $this->logger = $logger;
    }

    public function index($filePath)
    {
        if ( ( $handle = fopen($filePath, "r") ) !== false ) {
            $this->schoolRepository->truncateSchoolTable();
            $rowCount = 0;
            while ( ( $data = fgetcsv($handle) ) !== false ) {
                if ($rowCount > 0) {
                    $this->createSchoolData($data);
                }
                $rowCount++;
            }
            fclose($handle);
        }
    }

    public function createSchoolData(array $result): School
    {
        $school = new School();
        $school->setSchoolName($result[0]);
        $school->setStreet($result[1]);
        $school->setCity($result[2]);
        $school->setState($result[3]);
        $school->setLevel($result[4]);
        $school->setGrades($result[5]);
        $school->setLang($result[6]);
        $school->setPublic($result[7] == 'true');
        $school->setProgram($result[9]);
        $school->setWebUrl($result[10]);
        $school->setCoordinates(new Point(floatval($result[11]), floatval($result[12])));
        $school->setAreas($this->createPolygon($result));

        $this->entityManager->persist($school);

        $this->entityManager->flush();

        return $school;
    }

    public function getAreasXmlString(array $data): ?string
    {
        $result = null;
        if ($data[13] != ';' && $data[13] != '') {
            $xml = new SimpleXMLElement(trim($data[ 13 ], ';'));
            $result = $xml->Polygon->outerBoundaryIs->LinearRing->coordinates->__toString();
        }

        return $result;

    }

    public function createPolygon(array $result): ?Polygon
    {
        try {
            $areasString = trim($this->getAreasXmlString($result));
            if ( !empty($areasString) ) {
                $areasArray = explode(' ', trim($areasString, ' '));
                $polygonArray = [];
                foreach ( $areasArray as $point ) {
                    $pointArray = explode(',', $point);
                    if ( count($pointArray) != 2 ) {
                        throw new \Exception('Invalid point in polygon :: ' . $point);
                    }
                    $polygonArray[] = new Point(floatval($pointArray[ 0 ]), floatval($pointArray[ 1 ]));
                }
                return new Polygon($polygonArray);
            }
        } catch (\Exception $e) {
            $this->logger->error('Failed to parse areas for ' . $result[0] . ' - ' . $result[2] . ' - ' . $result[3] . ' :: ' . $e->getMessage());
        }
        return null;
    }
}