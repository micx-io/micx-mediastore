<?php

namespace App\Type;

class BlobIndexMedia
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $sha;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var string[]
     */
    public $tags = [];

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $uploadDate;

    /**
     * @var int|null
     */
    public $width;

    /**
     * @var int|null
     */
    public $height;

    /**
     * @var BlobIndexMediaVariant[]
     */
    public $variant = [];

    /**
     * @var string
     */
    public $license = "";

    /**
     * @var string
     */
    public $origUrl = "";

    /**
     * @var string
     */
    public $previewUrl = "";
}
