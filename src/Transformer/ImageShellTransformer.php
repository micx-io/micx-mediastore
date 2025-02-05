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
        phore_exec("convert ':input' -auto-orient -quality {quality} -resize '{width}x' ':output'", [
            "input" => $this->lastInputFile,
            "width" => $width,
            "quality" => $quality,
            "output" => $outName
        ]);

        $this->filenames[] = $outName;
        $this->lastInputFile = $outName;

        return $outName;

    }

    public function __destruct()
    {
        foreach ($this->filenames as $file) {
            unlink($file);
        }
    }

}
