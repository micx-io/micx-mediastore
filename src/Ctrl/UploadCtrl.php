<?php

namespace App\Ctrl;

use App\Business\StorageFacet;
use Brace\Router\Attributes\BraceRoute;
use Laminas\Diactoros\ServerRequest;

class UploadCtrl
{




    #[BraceRoute("POST@/{subscription_id}/{scope_id}/upload", "api.upload")]
    public function doUplod(ServerRequest $request, StorageFacet $storageFacet) {
        set_time_limit(300);
        ignore_user_abort(true);

        $quality = $request->getQueryParams()["quality"] ?? 85;

        $storageFacet->setStorageProcessorInstructions([
            "quality" => $quality
        ]);

        $ret = $request->getBody();
        foreach ($_FILES as $key => $file) {
            $tempName = $file["tmp_name"];
            $name = $file["name"];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $name = pathinfo($name, PATHINFO_FILENAME);
            // Replace Umlaute by their ascii representation
            $name = str_replace(["ä", "ö", "ü", "ß", " "], ["ae", "oe", "ue", "ss", ""], $name);
            $name = preg_replace("/[^a-zA-Z0-9_\-]/", "_", $name);
            $name = $name . "." . $ext;
            $error = $file["error"];
            if ($error !== 0) {
                throw new \HttpException("Upload failed with code: $error");
            }
            out("upload $name");
            $storageFacet->store($name, $tempName);
        }
        return ["success" => true, $ret, $_FILES];

    }
}
