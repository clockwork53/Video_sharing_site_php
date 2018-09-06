<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 8/16/2018
 * Time: 7:05 PM
 */

require_once ("server.php");

if (isset($_POST["logout"])) {

    unset($_SESSION["username"]);
    unset($_SESSION["privilage"]);
    header("location: ../index.html");
}