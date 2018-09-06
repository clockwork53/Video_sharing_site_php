<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/20/2018
 * Time: 2:50 PM
 */

require_once ("server.php");
require_once __DIR__.'\..\vendor\autoload.php';

$cat = "مغز و اعصاب";
$video = new video();
$results = $video->getByCategory($cat);
$video_thumbs = array();
try{
    $getID3 = new getID3;
}catch (Exception $e) {
    array_push($errors, "ID3 failed");
}

for ($i = 0; $i < count($results); $i++){
    $ffmpeg = \FFMpeg\FFMpeg::create([
        'ffmpeg.binaries'  => __DIR__.'\..\vendor\bin\ffmpeg.exe',
        'ffprobe.binaries' => __DIR__.'\..\vendor\bin\ffprobe.exe',
    ]);
    $video_thumbs[$i] = $ffmpeg->open($results[$i]['perma_link']);
    $video_thumbs[$i]
        ->filters()
        ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
        ->synchronize();
    $video_thumbs[$i]
        ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
        ->save('thumbs\brain-frame'.$i.'.jpg');
    $video_thumbs[$i]
        ->gif(FFMpeg\Coordinate\TimeCode::fromSeconds(4), new FFMpeg\Coordinate\Dimension(320, 240), 3)
        ->save('thumbs\brain-frame'.$i.'.gif');
    $vid_data[$i] = $getID3->analyze($results[$i]['perma_link']);
    $vid_duration[$i] = $vid_data[$i]['playtime_string'];
    $vid_views[$i] = $video->getViews($results[$i]['vuid']);
    if(!$vid_views[$i])
        $vid_views[$i] = 0;
}
