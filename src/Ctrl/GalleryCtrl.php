<?php

namespace App\Ctrl;

use App\Config\MediaStoreConf;
use App\Type\StorageFacet;
use Brace\Router\Attributes\BraceRoute;
use Brace\Router\RoutableCtrl;
use Brace\Router\Router;

class GalleryCtrl
{

    #[BraceRoute("GET@/{subscription_id}/{scope_id}/index.json")]
    public function index(StorageFacet $storageFacet, MediaStoreConf $mediaStoreConf) {
        $index = $storageFacet->getIndex();
        $index->baseUrl = CONF_URL_PREFIX . "/" . $mediaStoreConf->scope . "/";
        return (array)$index;
    }
}
