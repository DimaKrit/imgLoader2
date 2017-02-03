<?php

use DimaKrit\TestProject\Download;


require_once  "./vendor/autoload.php";


class FirstTest extends PHPUnit_Framework_TestCase
{

    public function testFirst()
    {

        $obj = new Download();

        $url = 'https://storage.googleapis.com/imgfave/image_cache/1483572468343197.jpg';

        $obj->imageDownload($url);

    }
}