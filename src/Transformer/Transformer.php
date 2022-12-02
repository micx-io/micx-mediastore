<?php

namespace App\Transformer;

use App\Type\BlobIndexMedia;
use App\Type\StorageFacet;
use Phore\ObjectStore\ObjectStore;

interface Transformer
{

    public function isSuitable(string $extension);
    public function store(string $data, BlobIndexMedia $media, ObjectStore $objectStore, string $scope);

}
