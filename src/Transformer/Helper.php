<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use App\Type\BlobIndexMediaVariant;

class Helper
{

    static public function buildPathDetail(string $width, string $height, string $id, string $filename, string $variant) {
        return $variant . "/" . $id . "/" . $width . "x" . $height . "/" . $filename;
    }

    static public function buildPath(BlobIndexMedia $media, BlobIndexMediaVariant $variant = null) {
        if ($variant !== null) {
            return self::buildPathDetail($variant->width, $variant->height, $media->id, $media->name, "v");
        }
        return self::buildPathDetail($media->width, $media->height, $media->id, $media->name . "." . $media->extension, "o");
    }
}
