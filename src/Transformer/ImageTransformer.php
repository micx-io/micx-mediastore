<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use App\Type\BlobIndexMediaVariant;
use Phore\ObjectStore\ObjectStore;

class ImageTransformer implements Transformer
{

    const SCALES = [
        ["s.xl", 1920, 0, false],
        ["s.lg", 1200, 0, false],
        ["s.md", 992, 0, false],
        ["s.sm", 768, 0, false],
        ["s.xs", 480, 0, false],
        ["256crop", 256, 256, true]
    ];

    const ADD_FORMATS = [
        "avif"
    ];


    public function __construct(
        public ObjectStore $objectStore,
        public string $scope
    ) {}


    public function isSuitable(string $extension)
    {
        return in_array($extension, ["png", "jpg", "jpeg", "gif", "webp", "avif"]);
    }


    private function resize (string $data, int $w, int $h, bool $thumbnail, &$curW, &$curH, bool $force=false) : ?\Imagick{
        static $im = null;
        if ($im === null) {
            $im = new \Imagick();
            $im->readImageBlob($data);
        }


        if ($im->getImageGeometry()["width"] < $w && ! $force)
            return null;

        if ($thumbnail) {
            $im->cropThumbnailImage($w, $h);
        } else {
            $im->scaleImage($w, $h, false);
        }

        $im->setCompressionQuality(70);

        $curW = $im->getImageGeometry()["width"];
        $curH = $im->getImageGeometry()["height"];

        return $im;
    }


    private function pushVariant (string $data, array $scale, array $formats, BlobIndexMedia $media)
    {
        $imagick = $this->resize($data, $scale[1], $scale[2], $scale[3], $curW, $curH);
        if ($imagick === null)
            return;

        $variantIndex = new BlobIndexMediaVariant();
        $variantIndex->height = $curH;
        $variantIndex->width = $curW;
        $variantIndex->extensions = $formats;
        $variantIndex->variantId = $scale[0];
        $variantIndex->url = Helper::buildPath($media, $variantIndex);
        $media->variant[] = $variantIndex;

        foreach($formats as $format) {
            $imagick->setFormat($format);
            $this->objectStore->object($this->scope . "/" . $variantIndex->url . "." . $format)->put($imagick->getImageBlob());
        }

    }


    public function store(string $data, BlobIndexMedia $media)
    {

        $im = new \Imagick();
        $im->readImageBlob($data);

        $media->width = $im->getImageGeometry()["width"];
        $media->height = $im->getImageGeometry()["height"];

        $media->origUrl = Helper::buildPath($media);

        $variants = self::ADD_FORMATS;
        out ("formate", $im->getImageFormat());
        $variants[] = strtolower($im->getImageFormat());

        foreach(self::SCALES as $scale) {
            out("push scale", $scale);
            $this->pushVariant($data, $scale, $variants, $media);
        }


        // Main image
        $this->objectStore->object($this->scope. "/" . $media->origUrl)->put($data);

        // Preview
        $previewImagick = $this->resize($data, 280, 0, false, $pwWidth, $pwHeight, true);
        $previewImagick->setFormat("jpeg");
        $preview = new BlobIndexMediaVariant();
        $preview->height = $pwHeight;
        $preview->width = $pwWidth;
        $preview->extensions = ["jpg"];
        $preview->variantId = "preview";
        $preview->url = Helper::buildPath($media, $preview);
        $media->variant[] = $preview;
        $media->previewUrl = $preview->url . ".jpg";
        $this->objectStore->object($this->scope. "/" . $media->previewUrl)->put($previewImagick->getImageBlob());

    }
}
