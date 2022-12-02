<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use Phore\ObjectStore\ObjectStore;

class SvgTransformer implements Transformer
{


    public function store(string $data, BlobIndexMedia $media, ObjectStore $objectStore, string $scope)
    {
        $xmlget = simplexml_load_string($data);
        $xmlattributes = $xmlget->attributes();
        $media->width = (int) $xmlattributes->width;
        $media->height = (int) $xmlattributes->height;

        $origPath = Helper::buildPath($media);
        $objectStore->object($scope . "/" . $origPath)->put($data);

        $media->origUrl = $origPath;
        $media->previewUrl = $origPath;
    }

    public function isSuitable(string $extension)
    {
        if ($extension === "svg")
            return true;
        return false;
    }
}
