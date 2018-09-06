<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/16/2018
 * Time: 7:04 PM
 */

require_once ("server.php");


if(isset($_POST["signin"])){
    $user = new user();
    $user->login(
        $_POST["username"],
        $_POST["password"]
    );
    header("location: ../index.html");
   }
