<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 03.10.2020
 *
 * @package estateblock20
 */

namespace App\Service\Feed\Provider;


use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Console\Application;

class DdfListingMasterProvider
{
    private QueryBuilder $queryBuilder;
    private $database;

/*
    public function __construct(Application $application)
    {
        $this->database = $application['db'];
    }

    public function getQueryBuilder()
    {
        if (!isset($this->queryBuilder)) {
            $this->queryBuilder = new QueryBuilder($this->database);
        }
        return $this->queryBuilder;
    }
*/
}