<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 22.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


use App\Service\Listing\ListingConstants;
use App\Service\Provider\AwsProvider;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Transfer;

class AwsService
{
    private AwsProvider $awsProvider;

    public function __construct(AwsProvider $awsProvider)
    {
        $this->awsProvider = $awsProvider;
    }

    public function upload(string $source, string $destination)
    {
        // Where the files will be transferred to
        $dest = $_ENV['ESBL_DIGITAL_OCEAN_S3_API_ENDPOINT'] . $destination;
        $uploader = new Transfer($this->awsProvider->getClient(), $source, $dest, [
            'before' => function(\Aws\Command $command) {
                // Commands can vary for multipart uploads, so check which command
                // is being processed
                if ( in_array($command->getName(), [ 'PutObject', 'CreateMultipartUpload' ]) ) {
                    // Set custom cache-control metadata
                    $command['CacheControl'] = 'max-age=3600';
                    // Apply a canned ACL
                    $command['ACL'] = 'public-read';
                }
            },
        ]);
        $uploader->transfer();
        unset($uploader);
    }

    public function delete(string $path)
    {
        $key = $this->awsProvider->getKeyName($path);
        $this->awsProvider->getClient()->deleteMatchingObjects(
            $this->awsProvider->getBucket(),
            $key
        );
    }

}