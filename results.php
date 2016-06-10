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

        $appearance=[];

        $personInfo = [
            'name'      =>$personName,
            'photo'     =>$personPhoto,
            'appearance'=>$appearance
        ];

        return $personInfo;
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