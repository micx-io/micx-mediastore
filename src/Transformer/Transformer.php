<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;

interface Transformer
{

    public function isSuitable(string $extension);
    public function store(string $data, BlobIndexMedia $media);

}
