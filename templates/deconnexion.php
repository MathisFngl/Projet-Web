<?php 
    session_start(); 
    require_once 'bdd.php';
    include_once('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('UPDATE user SET token = ? WHERE token = ?');
        $requUser->execute(array(bin2hex(openssl_random_pseudo_bytes(64)),$_SESSION['user']));
    }
    //setcookie('token','',time()-3600);
    $_SESSION = array();
    session_destroy(); // on détruit la/les session(s), soit si vous utilisez une autre session, utilisez de préférence le unset()
    header('Location:index.php'); // On redirige
    die();