<?php

namespace App\Ctrl;

use App\Type\StorageFacet;
use Brace\Router\RoutableCtrl;
use Brace\Router\Router;

class GalleryCtrl implements RoutableCtrl
{

    public static function Routes(Router $router, string $mount, array $mw): void
    {
        $router->on("GET@$mount/index.json", [self::class, "index"]);
    }

    public function index(StorageFacet $storageFacet) {
        return (array)$storageFacet->getIndex();
    }
}
