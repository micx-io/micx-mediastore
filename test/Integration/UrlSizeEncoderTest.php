<?php

namespace Test;

use App\Transformer\UrlSizeEncoder;

class UrlSizeEncoderTest extends \PHPUnit\Framework\TestCase
{

    public function testImageEncode() {
        $im = new UrlSizeEncoder(123, "someFile");
        $im->setExtensions(["jpg", "png"]);
        $im->setAspectRatio(1200, 800);
        $im->setWidths([1200, 800, 400, 200]);
        echo $im->toString();
    }

}
