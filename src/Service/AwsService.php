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

    public function upload(string $path, string $destination = null)
    {
        // Where the files will be source from
        $source = sys_get_temp_dir() . ListingConstants::UPLOAD_LISTING_PIC_PATH . $path;

        // Where the files will be transferred to
        $dest = $this->awsProvider->getDest($destination) . $path;
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

    public function getListingOriginalImage(string $mls_num, string $pic_num, int $feed_id)
    {
        $key = $this->awsProvider->getKeyName() . $feed_id . DIRECTORY_SEPARATOR . $mls_num . DIRECTORY_SEPARATOR . $mls_num . '_' . $pic_num . '.jpg';
        $effectiveUri = NULL;
        try {
            $result = $this->awsProvider->getClient('edge')->getObject([
                'Bucket' => $this->awsProvider->getBucket(),
                'Key' => $key,
            ]);
            $effectiveUri = $result['@metadata']['effectiveUri'];
        } catch ( S3Exception $e ) {
            $effectiveUri = $this->getListingNoImage();
        }
        return $effectiveUri;
    }

    public function getListingThumbnail(string $mls_num, string $pic_num, int $feed_id, int $width, int $height)
    {
        $key = $this->awsProvider->getKeyName() . $feed_id . DIRECTORY_SEPARATOR . $mls_num . DIRECTORY_SEPARATOR . 'thumbs' . DIRECTORY_SEPARATOR . 'thumb_' . $mls_num . '_' . $pic_num . '_' . $width . '_' . $height . '.jpg';
            try {
                $result = $this->awsProvider->getClient('edge')->getObject([
                    'Bucket' => $this->awsProvider->getBucket(),
                    'Key' => $key,
                ]);
                $effectiveUri = $result['@metadata']['effectiveUri'];
            } catch ( S3Exception $e ) {
                $effectiveUri = $this->getListingOriginalImage($mls_num, $pic_num, $feed_id);
            }
        return $effectiveUri;
    }

    public function getListingNoImage()
    {
        $key = $this->awsProvider->getKeyName() . 'no-img.jpg';
        $result = $this->awsProvider->getClient('edge')->getObject([
            'Bucket' => $this->awsProvider->getBucket(),
            'Key' => $key,
        ]);
        return $result['@metadata']['effectiveUri'];
    }

    public function delete(string $path)
    {
        $key = $this->awsProvider->getKeyName($path);
        $this->awsProvider->getClient()->deleteMatchingObjects(
            $this->awsProvider->getBucket(),
            $key
        );
    }

    public function deleteSelected(array $ObjectKeys, string $path)
    {
        $key = $this->awsProvider->getKeyName($path);
        foreach ($ObjectKeys as $objectKey) {
            $agentKey = $key . $objectKey;
            $this->awsProvider->getClient()->deleteMatchingObjects(
                $this->awsProvider->getBucket(),
                $agentKey
            );
        }
    }
}