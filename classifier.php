<?php

//namespace FaceDetector;
/**
 * Created by PhpStorm.
 * User: almasry
 * Date: 6/10/16
 * Time: 5:30 AM
 */

use mikehaertl\shellcommand\Command;

class FaceDetector{

    private $videoDuration;
    private $videoDurationInSeconds;
    private $videoName;

    public function __construct()
    {
        $directory[] = dirname(__FILE__).DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR;
        $directory[] = dirname(__FILE__).DIRECTORY_SEPARATOR."faces".DIRECTORY_SEPARATOR;

        foreach ($directory as $dir){
            $this->whipeDirectory($dir);
        }

        sleep(3);
    }

    private function whipeDirectory($imagesDir){
        $files = glob($imagesDir.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    public function processVideo($videoName){

        $this->videoName = $videoName;

        $videoFile = dirname(__FILE__).DIRECTORY_SEPARATOR."videos".DIRECTORY_SEPARATOR.$this->videoName;

        /**
         *
        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));

        $video = $ffmpeg->open($videoFile);
        
        $video
            ->filters()->clip()
            ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
            ->synchronize();
        $video
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
            ->save('frame.jpg');
        $video
            ->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4')
            ->save(new FFMpeg\Format\Video\WMV(), 'export-wmv.wmv')
            ->save(new FFMpeg\Format\Video\WebM(), 'export-webm.webm');
         *
         * ***/

        $imagesDir = dirname(__FILE__).DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR;
        $ffmpegCommand = '/usr/bin/ffmpeg -i '.$videoFile.' -r  1/1 '.$imagesDir.'$filename%03d.jpeg';
        $command = new Command($ffmpegCommand);

        $this->grepVideoDuration($videoFile);
        $this->saveVideoInfo();

        if ($command->execute()) {

        }
        return $this;
    }

    public function saveVideoInfo(){

        $videoInfo = dirname(__FILE__).DIRECTORY_SEPARATOR.'videoInfo.json';

        $videoInfoFile = fopen($videoInfo, "wr+") or die("Unable to open file!");
        fwrite($videoInfoFile,
            json_encode(
                [
                    'videoDuration' => $this->videoDurationInSeconds,
                    'videoURL'      => DIRECTORY_SEPARATOR.'videos'.DIRECTORY_SEPARATOR.$this->videoName
                ]
            ));
        fclose($videoInfoFile);
    }

    public function grepVideoDuration($videoFile){

        $durationCommand = '/usr/bin/ffmpeg -i  '.$videoFile.'  2>&1 | grep Duration | awk \'{print $2}\' | tr -d , ';
        $command = new Command($durationCommand);

        if ($command->execute()) {
            $this->videoDuration = $command->getOutput();
            $this->videoDurationInSeconds = (strtotime($this->videoDuration) - strtotime('TODAY'))+2;
        }
    }

    public function itterateImages(){

        $dirName = dirname(__FILE__).DIRECTORY_SEPARATOR."images".DIRECTORY_SEPARATOR;
        $dir = new \DirectoryIterator($dirName);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $file = $dirName.$fileinfo->getFilename();
                $this->detectFace($file);
            }
        }
        return $this;
    }

    public function detectFace($file){

        $command = new Command('./facedetect '.$file);
        if ($command->execute()) {

            if (!empty($command->getOutput())){

                foreach(preg_split("/((\r?\n)|(\r\n?))/", $command->getOutput()) as $face){
                    $faceDimentions = explode(' ', $face);

                    if(sizeof($faceDimentions)>3){
                        //print_r($faceDimentions);

                        $this->cropImage($file, $faceDimentions[0], $faceDimentions[1], $faceDimentions[2], $faceDimentions[3]);
                    }
                    
                }
            }
        } else {
            echo $command->getError();
            $exitCode = $command->getExitCode();
        }
    }

    public function cropImage($imagePath, $startX, $startY, $width, $height) {

        ///usr/local/bin/facerec  FACEREC

        if($width <110 || $height<110)
            return;

        $imagePath= realpath(trim($imagePath));

        $imagick = new \Imagick($imagePath);
        $imagick->cropImage($width, $height, $startX, $startY);

        $newFile = dirname(__FILE__).DIRECTORY_SEPARATOR."faces".DIRECTORY_SEPARATOR.rand(1,100).".jpg";

        $imagick->setImageFormat("jpeg");
        file_put_contents ($newFile, $imagick->getImageBlob()); // works, or:

        $imagick->destroy();
        sleep(1);

    }

    public function clusterPhotos(){
        $command = new Command('/home/almasry/projects/php/FaceDetector/comparison/openface/demos/compare.py '.$file);
        if ($command->execute()) {

            if (!empty($command->getOutput())){

                foreach(preg_split("/((\r?\n)|(\r\n?))/", $command->getOutput()) as $face){
                    $faceDimentions = explode(' ', $face);

                    if(sizeof($faceDimentions)>3){
                        //print_r($faceDimentions);

                        $this->cropImage($file, $faceDimentions[0], $faceDimentions[1], $faceDimentions[2], $faceDimentions[3]);
                    }

                }
            }
        } else {
            echo $command->getError();
            $exitCode = $command->getExitCode();
        }
    }

}