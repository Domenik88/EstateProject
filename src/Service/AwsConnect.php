<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

class AwsConnect
{
    private S3Client $s3Client;
    protected string $key;
    protected string $secret;
    protected string $region;
    protected string $endpoint;
    protected Credentials $credentials;
    protected string $dest;
    protected string $bucket;
    protected string $keyname;

    public function __construct(S3Client $s3Client, Credentials $credentials)
    {
        $this->s3Client = $s3Client;
        $this->credentials = $credentials;
        $this->key = getenv('ESBL_DIGITAL_OCEAN_KEY');
        $this->secret = getenv('ESBL_DIGITAL_OCEAN_SECRET');
        $this->region = getenv('ESBL_DIGITAL_OCEAN_REGION');
        $this->endpoint = getenv('ESBL_DIGITAL_OCEAN_ENDPOINT');
        $this->dest = getenv('ESBL_DIGITAL_OCEAN_W3_ENDPOINT') . getenv('ESBL_DIGITAL_OCEAN_W3_DEST');
        $this->bucket = getenv('ESBL_DIGITAL_OCEAN_W3_BUCKET');
        $this->keyname = getenv('ESBL_DIGITAL_OCEAN_W3_DEST');
    }

    public function connect()
    {
        if ($this->s3Client) {
            return;
        }

    }
}