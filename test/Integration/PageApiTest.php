<?php

class PageApiTest extends \PHPUnit\Framework\TestCase
{
    public function testGetPage()
    {
        $data = phore_http_request("http://localhost/v1/pagebuilder/demo1/weba/pages/home/home?lang=de")->send()->getBodyJson();
        print_r ($data);
    }
}
