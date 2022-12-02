<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use Phore\ObjectStore\ObjectStore;

class SvgTransformer implements Transformer
{


    public function __construct(
        public ObjectStore $objectStore,
        public string $scope
    ){}

    public function store(string $data, BlobIndexMedia $media)
    {
        $xmlget = simplexml_load_string($data);
        $xmlattributes = $xmlget->attributes();

        if ($xmlattributes->viewBox != "") {
            [$dummy, $dummy, $media->width, $media->height] = explode(" ", $xmlattributes->viewBox);
        } else {
            $media->width = (int) $xmlattributes->width;
            $media->height = (int) $xmlattributes->height;
        }

        $origPath = Helper::buildPath($media);
        $this->objectStore->object($this->scope . "/" . $origPath)->put($data);

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
