<?php 
require_once 'config2.php';
class Logout {
    public function keluar(){
        if (session_status()=== PHP_SESSION_NONE){
            session_start(); 
        }
        $_SESSION = [];
     session_destroy();
     header("Location: login.php");
     exit;
    }
}
$logout = new Logout(); 
$logout->keluar();
?>