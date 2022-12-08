<?php

namespace App\Type;

class BlobIndex
{
    /**
     * @var BlobIndexMedia[]
     */
    public $media = [];

    /**
     * @var int
     */
    public int $lastId = 1;

    /**
     * @var string
     */
    public $scope = "";

    /**
     * @var string
     */
    public $baseUrl = "";
}
