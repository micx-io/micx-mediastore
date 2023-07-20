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

    /**
     * Return the Aspect Ratio of the given width and height
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    static public function getAspectRatio(int $width, int $height) : string {
        $gcdFn = function ($a, $b) use (&$gcdFn) {
            if ($b == 0) {
                return $a;
            } else {
                return $gcdFn($b, $a % $b);
            }
        };


        if ($height == 0) {
            return $width . "/" . $height;
        }

        $gcd = $gcdFn($width, $height);

        $width_ratio = $width / $gcd;
        $height_ratio = $height / $gcd;

        return $width_ratio . "/" . $height_ratio;

    }

}
