<?php
session_start();

require_once "connection.php";

$errors = array();

/**
 * 30 minute lifetime for cookies
 */
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // session started more than 30 minutes ago
    session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
    $_SESSION['CREATED'] = time();  // update creation time
}

class user {
    private $_username;
    private $_password;
    private $_email;
    private $_tel;
    private $_fName;
    private $_lName;
    private $_alma;
    private $_uuid;
    private $_type;
    function login($a, $b) {
        global $conn;
        global $errors;
        $this->_username = $a;
        $this->_password = $b;
        try{
            $sql = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $sql->execute(array($this->_username, $this->_password));
            $results = $sql->fetchAll(PDO::FETCH_ASSOC);
            if (!$results) {
                array_push($errors, "Username and password don't match");
            } elseif ( count($results) == 1) {
                $row = $results[0];
                $this->_username = $row["username"];
                $this->_password = $row["password"];
                $this->_email = $row["email"];
                $this->_tel = $row["telNum"];
                $this->_fName = $row["fName"];
                $this->_lName = $row["lName"];
                $this->_alma = $row["alma"];
                $this->_type = $row["type"];
                $this->_uuid = $row["uuid"];
                $_SESSION["username"] = $this->_username;
                $_SESSION["privilage"] = $this->_type;
            }
        }catch (PDOException $e){
            array_push($errors, "Could not login");
            array_push($errors, $e->getMessage());
        }
    }
    function addUser($un, $pw, $em, $t, $fn, $ln, $al) {
        global $conn;
        global $errors;
        $this->_username = $un;
        $this->_password = $pw;
        $this->_email = $em;
        $this->_tel = $t;
        $this->_fName = $fn;
        $this->_lName = $ln;
        $this->_alma = $al;
        $this->_type = 3;
        try {
            $sql = $conn->prepare(
                "INSERT INTO users (fName, lName, telNum, email, username, password, alma, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->execute(
                array($this->_fName, $this->_lName, $this->_tel, $this->_email, $this->_username, $this->_password, $this->_alma, $this->_type));

            $_SESSION["username"] = $this->_username;
            $_SESSION["privilage"] = $this->_type;

        }catch (PDOException $e){
            array_push($errors,"Could not add new User");
            array_push($errors, $e->getMessage());
        }
    }
}

class video {
    private $_title;
    private $_vuid;
    private $_category;
    //private $_upload_date;
    private $_permalink;
    private $_description;
    private $_uuid;
    private $_keywords;
    private $_doctor_name;
    function addVideoBig($link){
        $this->_permalink = $link;
    }
    function addVideo($t, $cat, $desc, $username, $kw, $doc) {
        global $conn;
        global $errors;
        $this->_title = $t;
        $this->_category = $cat;
        $this->_description = $desc;
        $this->_uuid;
        $this->_keywords = $kw;
        $this->_doctor_name = $doc;
        try{
            $sql = $conn->prepare(
                "SELECT uuid FROM users WHERE username = ?");
            $sql->execute(array($username));
            $this->_uuid = $sql->fetchAll(PDO::FETCH_ASSOC);
            $this->_uuid = $this->_uuid[0];
            $this->_uuid = $this->_uuid["uuid"];

            $sql = null;
            $sql = $conn->prepare(
                "INSERT INTO videos (title, category, description, uuid, perma_link, keywords, doctor_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $sql->execute(
                array($this->_title, $this->_category, $this->_description, $this->_uuid, $this->_permalink, $this->_keywords, $this->_doctor_name));

        }catch (PDOException $e){
            array_push($errors, "Could not add video to database");
            array_push($errors, $e->getMessage());
        }
    }
    function getRecent()
    {
        global $conn;
        global $errors;
        try{
                $sql = $conn->prepare(
                "SELECT * FROM videos ORDER BY upload_date DESC LIMIT 8");
                $sql->execute();
                $results = $sql->fetchAll(PDO::FETCH_ASSOC);
                if(!$results){
                    array_push($errors, "no video found ?!? de faq?");
                }else{
                    return $results;
                }
        }catch (PDOException $e){
            array_push($errors, "Could not connect to database");
            array_push($errors, $e->getMessage());
        }
    }
    function getByCategory($cat){
        global $conn;
        global $errors;
        $this->_category = $cat;
        try{
            $sql = $conn->prepare(
                "SELECT * FROM videos WHERE category = ? ORDER BY upload_date DESC LIMIT 4");
            $sql->execute(
                array($this->_category));
            $results = $sql->fetchAll(PDO::FETCH_ASSOC);
            if(!$results){
                array_push($errors, "no video found ?!? de faq?");
            }else{
                return $results;
            }
        }catch (PDOException $e){
            array_push($errors, "Could not connect to database");
            array_push($errors, $e->getMessage());
        }
    }
    function getViews($vuid_param){
        global $conn;
        global $errors;
        $this->_vuid = $vuid_param;
        try{
            $sql = $conn->prepare(
                "SELECT DISTINCT count(vwv.uuid) AS views FROM
                (SELECT videos.vuid, wv.vuid, wv.uuid FROM videos JOIN watched_videos wv on videos.vuid = wv.vuid) AS vwv
                WHERE vwv.vuid = ?");
            $sql->execute(
                array($this->_vuid));
            $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql = $sql[0]["views"];
            return $sql;
        }catch (PDOException $e){
            array_push($errors, "Could not get views");
            array_push($errors, $e->getMessage());
        }
    }
    function getVideoByVUID($vuid_parm){
        global $conn;
        global $errors;
        $this->_vuid = $vuid_parm;
        try{
            $sql = $conn->prepare(
                "SELECT * FROM videos WHERE vuid = ?");
            $sql->execute(
                array($this->_vuid));
            $sql = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $sql[0];

        }catch (PDOException $e){
            array_push($errors, "Could not get video");
            array_push($errors, $e->getMessage());
        }
    }

}