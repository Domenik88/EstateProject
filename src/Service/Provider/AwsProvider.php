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
    protected string $endpointEdge;
    protected string $dest;
    protected string $bucket;
    protected string $keyName;

    public function __construct()
    {
        $this->key = getenv('ESBL_DIGITAL_OCEAN_KEY');
        $this->secret = getenv('ESBL_DIGITAL_OCEAN_SECRET');
        $this->region = getenv('ESBL_DIGITAL_OCEAN_REGION');
        $this->endpoint = getenv('ESBL_DIGITAL_OCEAN_ENDPOINT');
        $this->endpointEdge = getenv('ESBL_DIGITAL_OCEAN_ENDPOINT_EDGE');

        $this->dest = getenv('ESBL_DIGITAL_OCEAN_W3_ENDPOINT') . 'listings/';
        $this->bucket = getenv('ESBL_DIGITAL_OCEAN_W3_BUCKET');
        $this->keyName = getenv('ESBL_DIGITAL_OCEAN_W3_DEST');
    }

    public function connect($endpoint = null)
    {
        if (!is_null($endpoint)){
            $endpoint = $this->endpointEdge;
        } else {
            $endpoint = $this->endpoint;
        }
        if ($this->s3Client) {
            return;
        }
        $this->credentials = new Credentials($this->key, $this->secret);

        $options = [
            'version' => 'latest',
            'region' => $this->region,
            'endpoint' => $endpoint,
            'credentials' => $this->credentials,
            'debug' => getenv('ESBL_DIGITAL_OCEAN_DEBUG'),
        ];
        $this->s3Client = new S3Client($options);
    }

    public function getClient($endpoint = null): S3Client
    {
        if (!$this->s3Client) {
            $this->connect($endpoint);
        }
        return $this->s3Client;
    }

    public function getKeyName($destination = null)
    {
        if (!is_null($destination)) {
            return $destination;
        } else {
            return $this->keyName;
        }
    }

    public function getBucket()
    {
        return $this->bucket;
    }

    public function getDest($destination = null)
    {
        if (!is_null($destination)) {
            return $destination;
        } else {
            return $this->dest;
        }
    }
}