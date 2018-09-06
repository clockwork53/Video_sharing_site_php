<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/16/2018
 * Time: 10:08 PM
 */

require_once ("server.php");


if(isset($_POST["signup"])){
    $user = new user();
    $user->addUser(
        $_POST["username"],
        $_POST["password"],
        $_POST["email"],
        $_POST["telNum"],
        $_POST["fName"],
        $_POST["lName"],
        $_POST["alma"]
    );

    header('location: ../index.html');
}