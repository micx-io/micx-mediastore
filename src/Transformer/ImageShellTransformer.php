<?php

namespace App\Transformer;

class ImageShellTransformer
{

    public function __construct(private string $inputFile)
    {
    }

    private $dimensions = null;
    private $lastInputFile = null;

    public function getImageMimeType() : string {
        $ret = phore_exec("identify -auto-orient -format '%m' ':file' 2>/dev/null", ["file" => $this->inputFile]);
        return $ret;
    }

    public function getImageDimensions() : array {
        if ($this->dimensions !== null)
            return $this->dimensions;
        $ret = phore_exec("identify -auto-orient -format '%w %h' ':file' 2>/dev/null", ["file" => $this->inputFile], true);

        $ret = $ret[count($ret)-1]; // Ingore warnings tirggered by webp

        $exp = explode(" ", $ret);
        $this->dimensions = [
            "width" => (int)$exp[0],
            "height" => (int)$exp[1]
        ];
        return $this->dimensions;
    }


    public $filenames = [];

    public function convert(string $format, int $width, int $quality) : string {
        if ( ! $this->lastInputFile)
            $this->lastInputFile = $this->inputFile;

        $inputDir = dirname($this->lastInputFile);
        $inputName = basename($this->lastInputFile);

        $outName = $inputDir . "/" .  $inputName . "-" . $width . "."  . $format;
        $outName100q = $inputDir . "/" .  $inputName . "-" . $width . "-100q."  . $format;

        // To prevent qualtity losses in lower resolutions first resize to 100% quality (Exif Daten löschen)
        phore_exec("gm convert ':input' -auto-orient -quality {quality} -resize '{width}x' +profile exif ':output'", [
            "input" => $this->lastInputFile,
            "width" => $width,
            "quality" => 100,
            "output" => $outName100q
        ]);

        $this->filenames[] = $outName100q;
        $this->lastInputFile = $outName100q;

        // Then reduce quality / Do not use -strip, because this will remove COLOR Palette
        phore_exec("gm convert ':input' -define {method} -define {filter} -define {alpha-filter} -define {alpha-compression} -quality {quality} ':output'", [
            "input" => $outName100q,
            "quality" => $quality,
            "method" => "webp:method=6",
            "filter" => "webp:auto-filter=true",
            "alpha-compression" => "webp:alpha-compression=1",
            "alpha-filter" => "webp:alpha-filter=none",
            "output" => $outName
        ]);

        $this->filenames[] = $outName;

        return  $outName;

    }

    public function __destruct()
    {
        foreach ($this->filenames as $file) {
            unlink($file);
        }
    }

}
