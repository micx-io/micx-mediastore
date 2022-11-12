<?php

namespace App\Ctrl;

use App\Type\StorageFacet;
use Brace\Router\RoutableCtrl;
use Brace\Router\Router;
use Laminas\Diactoros\ServerRequest;

class UploadCtrl implements RoutableCtrl
{

    public static function Routes(Router $router, string $mount, array $mw): void
    {
        $router->on("POST@$mount/upload", [self::class, "doUplod"]);
    }


    protected function resize($input) : string {
        $im = new \Imagick();
        $im->readImageBlob($input);
        $im->getFormat();
        $im->scaleImage(900,900, true);

        return $im->getImageBlob();
    }

    public function doUplod(ServerRequest $request, StorageFacet $storageFacet) {
        $ret = $request->getBody();
        foreach ($_FILES as $key => $file) {
            $tempName = $file["tmp_name"];
            $name = $file["name"];
            $error = $file["error"];
            if ($error !== 0) {
                throw new \HttpException("Upload failed with code: $error");
            }
            out("upload $name");
            $storageFacet->storeImage($name, $this->resize(phore_file($tempName)->get_contents()));
        }

        return ["success" => true, $ret, $_FILES];

    }
}
