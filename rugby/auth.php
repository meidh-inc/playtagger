<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();

require_once("function.php");


$action = $_GET["action"];

if ($action == "login") {
    db_connect();
    $user = clean($_POST['user']);
    $pass = clean($_POST['pass']);
    
    
    $error_flag = false;
    
    if ($user == "" || $pass == "") {
        $error_msg = "No User ID or Password Inputted.";
        $error_flag = true;
    }
    else {
        $pass = md5($pass);
        $query = "SELECT * FROM users WHERE userName='$user' AND password='$pass'";
        $result = mysql_query($query);
        
        if (mysql_num_rows($result) == 0) {
            $error_msg = "Username or Password not found";
            $error_flag = true;
        }
        else if (mysql_num_rows($result) == 1) {
            session_regenerate_id();
            
            $_SESSION['userID'] = mysql_result($result,0,"userID");
            $_SESSION['userName'] = $user;
            $_SESSION['userType'] = mysql_result($result,0,"userType");
            $_SESSION['firstName'] = mysql_result($result,0,"firstName");
            $_SESSION['lastName'] = mysql_result($result,0,"lastName");
            
            writeLog("login", "success!", $user);
            
            session_write_close();
            
            header("location: combine.php");
            exit();
            
        }
        
        else {
            $error_msg = "Unknown Error";
            $error_flag = true;
        }
    }
    
    if ($error_flag) {
        $_SESSION['ERROR_MSG'] = $error_msg;
        session_write_close();
        header("Location: index.php");
    }
    
    db_close();
}

else if ($action == "logout") {
    writeLog("logout", "", $_SESSION['userName']);
    unset($_SESSION['userID']);
    unset($_SESSION['userName']);
    unset($_SESSION['userType']);
    unset($_SESSION['firstName']);
    unset($_SESSION['lastName']);
    
    session_destroy();
    
    header("Location: index.php");
}

/* else {
    if (!isset($_SESSION['userID']) || (trim($_SESSION['userID']) == "")) {
        $_SESSION['ERROR_MSG'] = $error_msg;
        session_write_close();
        header("Location: index.php");
    }
}
*/