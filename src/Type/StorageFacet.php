<?php

namespace App\Type;

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


    public function storeImage($name, $data) {
        if ( ! $this->isUnique($data))
            return false;

        $obj = new BlobIndexMedia();
        $obj->size = strlen($data);
        $obj->id = $this->index->lastId++;
        $obj->sha = sha1($data);

        [$obj->name, $obj->extension] = $this->splitExtension($name);

        $im = new \Imagick();
        $im->readImageBlob($data);

        $obj->width = $im->getImageGeometry()["width"];
        $obj->height = $im->getImageGeometry()["height"];

        $obj->origUrl = "orig/{$obj->id}_{$obj->name}_{$obj->width}x{$obj->height}.{$obj->extension}";

        $im->scaleImage(200, 200, true);
        $im->setFormat("jpeg");
        $im->setCompressionQuality(70);
        $obj->previewUrl = "prev/{$obj->id}_{$obj->name}_{$im->getImageGeometry()["width"]}x{$im->getImageGeometry()["height"]}.jpg";

        $this->publicStore->object($this->scope. "/" . $obj->origUrl)->put($data);
        $this->publicStore->object($this->scope. "/" . $obj->previewUrl)->put($im->getImageBlob());

        $this->index->media[] = $obj;
        $this->saveIndex();

    }

    public function storeRaw($name, $data) {

    }
}
