<?php 
#essentials
session_start();
include "../dbconfig.php";
include "../style.php";
#sessions
if(!isset($_SESSION['USER'])){
    header("location: ../logout.php");
}

if(isset($_SESSION['USER'])){
    $user_session = $_SESSION['USER'];
    if($user_session['user_role'] != "Admin"){
        header("location: ../Users/");
    }
}
#end of essentials
?>