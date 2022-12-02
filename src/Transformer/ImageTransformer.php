<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use App\Type\BlobIndexMediaVariant;
use Phore\ObjectStore\ObjectStore;

class ImageTransformer implements Transformer
{
    public function isSuitable(string $extension)
    {
        return in_array($extension, ["png", "jpg", "jpeg", "gif", "webp", "avif"]);
    }
    public function store(string $data, BlobIndexMedia $media, ObjectStore $objectStore, string $scope)
    {
        $im = new \Imagick();
        $im->readImageBlob($data);

        $media->width = $im->getImageGeometry()["width"];
        $media->height = $im->getImageGeometry()["height"];

        $media->origUrl = Helper::buildPath($media);

        $im->scaleImage(280, 280, true);
        $im->setFormat("jpeg");
        $im->setCompressionQuality(70);

        $preview = new BlobIndexMediaVariant();
        $preview->height = $im->getImageGeometry()["height"];
        $preview->width = $im->getImageGeometry()["width"];
        $preview->extensions = ["jpg"];
        $preview->variantId = "preview";
        $preview->url = Helper::buildPath($media, $preview);
        $media->variant[] = $preview;
        $media->previewUrl = $preview->url;

        // Main image
        $this->publicStore->object($scope. "/" . $media->origUrl)->put($data);

        // Preview
        $this->publicStore->object($scope. "/" . $media->previewUrl)->put($im->getImageBlob());

    }
}
