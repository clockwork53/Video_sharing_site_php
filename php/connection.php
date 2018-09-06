<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/16/2018
 * Time: 6:54 PM
 */

require_once "config.php";

$notice_arr = array();

try{
    $conn = new PDO("mysql:host=$serverName;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    array_push($notice_arr,"sql Connection Successful");
}catch(PDOException $e){
    array_push($notice_arr,"sql Connection Failed". $e->getmessage());
}
