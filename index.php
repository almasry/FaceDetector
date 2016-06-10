<?php

/**
 * Created by PhpStorm.
 * User: almasry
 * Date: 6/10/16
 * Time: 5:42 AM
 */

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/classifier.php';

//use FaceDetector\FaceDetector;

$videoFile = 'video1.mp4';

(new FaceDetector())
    ->processVideo( dirname(__FILE__).DIRECTORY_SEPARATOR."videos".DIRECTORY_SEPARATOR.$videoFile)
    ->itterateImages()
;