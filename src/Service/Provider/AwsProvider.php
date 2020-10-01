<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 23.09.2020
 *
 * @package estateblock20
 */

namespace App\Service\Provider;


use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsProvider
{
    protected Credentials $credentials;
    protected S3Client $s3Client;
    protected string $key;
    protected string $secret;
    protected string $region;
    protected string $endpoint;
    protected string $bucket;

    public function __construct()
    {
        $this->key = $_ENV['ESBL_DIGITAL_OCEAN_KEY'];
        $this->secret = $_ENV['ESBL_DIGITAL_OCEAN_SECRET'];
        $this->region = $_ENV['ESBL_DIGITAL_OCEAN_REGION'];
        $this->endpoint = $_ENV['ESBL_DIGITAL_OCEAN_ENDPOINT'];
        $this->bucket = $_ENV['ESBL_DIGITAL_OCEAN_S3_API_BUCKET'];
    }

    private function connect()
    {
        $this->credentials = new Credentials($this->key, $this->secret);

        $options = [
            'version' => 'latest',
            'region' => $this->region,
            'endpoint' => $this->endpoint,
            'credentials' => $this->credentials,
        ];
        $this->s3Client = new S3Client($options);
    }

    public function getClient(): S3Client
    {
        if (!isset($this->s3Client)) {
            $this->connect();
        }
        return $this->s3Client;
    }

    public function getBucket()
    {
        return $this->bucket;
    }

}