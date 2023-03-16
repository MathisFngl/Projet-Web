<?php 
    session_start(); 
    //setcookie('token','',time()-3600);
    $_SESSION = array();
    session_destroy(); // on détruit la/les session(s), soit si vous utilisez une autre session, utilisez de préférence le unset()
    header('Location:index.php'); // On redirige
    die();