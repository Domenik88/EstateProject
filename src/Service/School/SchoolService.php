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
use App\Service\Geo\Point;
//use App\Service\Geo\Polygon;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SchoolService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    public function index($filePath)
    {
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            try {
                //                $this->entityManager->getConnection()->beginTransaction();
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $school = new School();
                    $school->setSchoolName($data[0]);
                    $school->setStreet($data[1]);
                    $school->setCity($data[2]);
                    $school->setState($data[3]);
                    $school->setLevel($data[4]);
                    $school->setGrades($data[5]);
                    $school->setLang($data[6]);
                    $school->setPublic($data[7] == 'true');
                    $school->setProgram($data[9]);
                    $school->setWebUrl($data[10]);
                    $school->setCoordinates(new Point($data[11],$data[12]));

                    $this->entityManager->persist($school);

                    $this->entityManager->flush();
                }
//                $this->entityManager->flush();
//                $this->entityManager->getConnection()->commit();
            } catch ( Exception $e) {
                $this->entityManager->getConnection()->rollBack();
                $this->logger->error($e->getMessage());
                throw $e;
            } finally {
                fclose($handle);
            }
        }
    }
}