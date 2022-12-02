<?php

namespace App\Type;

use App\Transformer\ImageTransformer;
use App\Transformer\SvgTransformer;
use App\Transformer\Transformer;
use Phore\ObjectStore\ObjectStore;

class StorageFacet
{

    /**
     * @var BlobIndex
     */
    public $index;
    public $indexObj;

    public function __construct(
        public ObjectStore $publicStore,
        public ObjectStore $privateStore,
        public string      $scope) {

        $this->indexObj = $this->privateStore->object($this->scope . "/.index.media.json");
        if ( ! $this->indexObj->exists()) {
            $this->index = new BlobIndex();
        } else {
            $this->index = phore_hydrate($this->indexObj->getJson(), BlobIndex::class);
        }

    }

    protected function sanitizeName($name) {
        return preg_replace("/[^a-z0-9_\-\.]/im", "", $name);
    }

    public function saveIndex() {
        out("Save Index");
        $this->indexObj->putJson((array)$this->index);
    }

    protected function splitExtension($filename) {
        return [pathinfo($filename, PATHINFO_FILENAME), pathinfo($filename, PATHINFO_EXTENSION)];
    }

    protected function isUnique($data) {
        $sha = sha1($data);
        foreach ($this->index->media as $media) {
            if ($media->sha === $sha)
                return false;
        }
        return true;
    }


    public function getIndex() {
        return $this->index;
    }





    public function storeImage($name, $data) {
        if ( ! $this->isUnique($data))
            return false;

        $obj = new BlobIndexMedia();
        $obj->size = strlen($data);
        $obj->id = $this->index->lastId++;
        $obj->sha = sha1($data);

        [$obj->name, $obj->extension] = $this->splitExtension($name);

        $transformers = [
            new SvgTransformer($this->publicStore, $this->scope),
            new ImageTransformer($this->publicStore, $this->scope),
        ];

        foreach ($transformers as $transformer) {
            assert($transformer instanceof Transformer);
            if ( ! $transformer->isSuitable($obj->extension))
                continue;
            $transformer->store($data, $obj);
        }

        array_unshift($this->index->media, $obj);
        $this->saveIndex();

    }

}
