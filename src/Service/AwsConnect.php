<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use App\Service\Provider\AwsProvider;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsConnect
{
    private AwsProvider $awsProvider;

    public function __construct(AwsProvider $awsProvider)
    {
        $this->awsProvider = $awsProvider;
    }

}