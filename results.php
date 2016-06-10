<?php
/**
 * Created by PhpStorm.
 * User: almasry
 * Date: 6/10/16
 * Time: 11:00 AM
 */

(new ResultsMatecher())->getEncodedJsonResult();

class ResultsMatecher
{

    private $videoDuration;
    private $videoURL;

    public function __construct()
    {
        $this->getVideoDetails();
    }

    public function getPersonsInfo($personName, $rootDir){

        $dirName = dirname(__FILE__).$rootDir.DIRECTORY_SEPARATOR.$personName;
        $dir = new \DirectoryIterator($dirName);

        $personPhoto ='';

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $personPhoto = $rootDir.DIRECTORY_SEPARATOR.$personName.DIRECTORY_SEPARATOR.$fileinfo->getFilename();
            }
        }

        $appearance= $this->calculateAppearanceSpans($dirName);

        $personInfo = [
            'name'      =>$personName,
            'photo'     =>$personPhoto,
            'appearance'=>$appearance
        ];

        return $personInfo;
    }

    public function calculateAppearanceSpans($personDirectory)
    {
        $timePoints =[];

        $personDirectory = new \DirectoryIterator($personDirectory);
        foreach ($personDirectory as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $timePoints[] = substr($fileinfo->getFilename(), 0, -4);
            }
        }
        asort($timePoints);

        return($this->calculateTimeSpans($timePoints));
    }



    public function calculateTimeSpans($timePoints) {

        $timeSpans = [];

        $timeSpanStart = 0;
        $timeSpanEnd = 0;

        foreach($timePoints as $timePoint) {

            // check the start of a new timepan
            if($timeSpanStart == 0) {
                // initiate a timespan
                $timeSpanStart = $timePoint;
                $timeSpanEnd = $timePoint;
            } else {
                // check the end of a timespan
                if($timePoint - $timeSpanEnd > 1) {
                    // complete a timespan and write it in result array
                    if($timeSpanEnd > $timeSpanStart){
                        $timeSpans[] = ["start" => $timeSpanStart, "end" => $timeSpanEnd];
                    }

                    // reset
                    $timeSpanStart = $timePoint;
                    $timeSpanEnd = $timePoint;
                } else {
                    // check an extension of an running timespan
                    $timeSpanEnd = $timePoint;
                }
            }
        }
        return $timeSpans;
    }

    public function getEncodedJsonResult(){

        $persons =[];

        $rootDir = dirname(__FILE__).DIRECTORY_SEPARATOR."clustered_faces".DIRECTORY_SEPARATOR;
        $dir = new \DirectoryIterator($rootDir);

        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $personName = $fileinfo->getFilename();
                $persons[] = $this->getPersonsInfo($personName, DIRECTORY_SEPARATOR."clustered_faces".DIRECTORY_SEPARATOR);
            }
        }

        $array = [
            'videoDuration'=> $this->videoDuration,
            'videoURL'=> $this->videoURL,
            'people'=> $persons
        ];

        print(json_encode($array)).PHP_EOL;
    }

    public function getVideoDetails(){

        $videoInfo = dirname(__FILE__).DIRECTORY_SEPARATOR.'videoInfo.json';
        $videoInfoFile = fopen($videoInfo, "r") or die("Unable to open file!");
        $fileContent = fread($videoInfoFile, 4096);

        $videoInfo = json_decode($fileContent, true);

        $this->videoDuration = $videoInfo['videoDuration'];
        $this->videoURL = $videoInfo['videoURL'];
    }
}