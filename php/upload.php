<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/18/2018
 * Time: 6:37 PM
 */

require_once ("server.php");


if (isset($_FILES["video_file"])) {
    $allowedExts = array("gif", "mp4", "wma");
    $extension = strtolower(pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION));
    $ini_PostSize = preg_replace("/[^0-9,.]/", "", ini_get('post_max_size'))*(1024*1024);
    $ini_FileSize = preg_replace("/[^0-9,.]/", "", ini_get('upload_max_filesize'))*(1024*1024);
    $maxFileSize = ($ini_PostSize<$ini_FileSize ? $ini_PostSize : $ini_FileSize);
    $file = (isset($_FILES["video_file"]) ? $_FILES["video_file"] : 0);


    if ((($_FILES["video_file"]["type"] == "video/mp4")
          || ($_FILES["video_file"]["type"] == "audio/wma")
          || ($_FILES["video_file"]["type"] == "image/gif"))
        && ($_FILES["video_file"]["size"] < $maxFileSize)
        && in_array($extension, $allowedExts))

    {
        if ($_FILES["video_file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["video_file"]["error"] . "<br />";
        }
        elseif (file_exists("upload/" . $_FILES["video_file"]["name"])) {
                echo $_FILES["video_file"]["name"] . " already exists. ";
            }
            else {
                move_uploaded_file($_FILES["video_file"]["tmp_name"],__DIR__ . "\..\uploaded_vids\\" . $_FILES["video_file"]["name"]);
                $url = __DIR__ . "\..\uploaded_vids\\" . $_FILES["video_file"]["name"];
                $video = new video();
                $video->addVideoBig($url);

            }
        }
    }
    else {
        echo "Invalid file";
    }

$video->addVideo($_POST["videoTitle"], $_POST["category"], $_POST["description"], $_SESSION["username"], $_POST["keywords"], $_POST["doctor_name"]);

header("location: ../index.html");
/*
if(isset($_GET["getsize"])) {
    echo $maxFileSize;
    exit;
}
if (!$file) { // if file not chosen

    if($file["size"]>$maxFileSize){
        die("ERROR: The File is too big! The maximum file size is ".$maxFileSize/(1024*1024)."MB");
    }
    die("ERROR: Please browse for a file before clicking the upload button");
}
if($file["error"]) {

    die("ERROR: File couldn't be processed");

}
*/
/*
if(move_uploaded_file($file["tmp_name"], "test_uploads/".$file["name"])){
    echo "SUCCESS: The upload of ".$file["name"]." is complete";
} else {
    echo "ERROR: Couldn't move the file to the final location";
}*/