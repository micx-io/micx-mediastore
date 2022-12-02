<?php

namespace App\Ctrl;

use App\Type\StorageFacet;
use Brace\Router\Attributes\BraceRoute;
use Brace\Router\RoutableCtrl;
use Brace\Router\Router;
use Laminas\Diactoros\ServerRequest;

class UploadCtrl
{




    #[BraceRoute("POST@/{subscription_id}/{scope_id}/upload", "api.upload")]
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
            $storageFacet->storeImage($name, phore_file($tempName)->get_contents());
        }

        return ["success" => true, $ret, $_FILES];

    }
}
