<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 19.01.2021
 *
 * @package estateblock20
 */

namespace App\Service\School;

use App\Entity\School;
use App\Service\Geo\Point;

class SchoolData
{

    public string $name;
    public string $street;
    public string $city;
    public string $state;
    public string $level;
    public string $grades;
    public string $lang;
    public bool $public;
    public string $program;
    public string $uri;
    public Point $coordinates;
    public ?array $areas;
    public ?string $distance;

    public function __construct(School $school, ?string $distance)
    {
        $this->name = (string)$school->getSchoolName();
        $this->street = (string)$school->getStreet();
        $this->city = (string)$school->getCity();
        $this->state = (string)$school->getState();
        $this->level = (string)$school->getLevel();
        $this->grades = (string)$school->getGrades();
        $this->lang = (string)$school->getLang();
        $this->public = (bool)$school->getPublic();
        $this->program = (string)$school->getProgram();
        $this->uri = (string)$school->getWebUrl();
        $this->coordinates = $school->getCoordinates();
        $this->areas = $school->getAreas();
        $this->distance = $distance;

        return $this;
    }

}