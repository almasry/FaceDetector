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

$videoName = 'Video2.mp4';

(new FaceDetector())
    ->processVideo($videoName)
    ->itterateImages()
    ->clusterPhotos()
;