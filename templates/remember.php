<?php 
    require_once 'bdd.php';
    if(isset($_COOKIE['user']) && !isset($_SESSION['user'])){
        $token=$_COOKIE['user'];
        $reqRemember = $bdd->prepare('SELECT * FROM user WHERE token = ?');
        $reqRemember->execute(array($token));
        $rememberUser = $reqRemember->fetch();
        if($rememberUser){
            $_SESSION['user'] = $rememberUser['token'];
            $_SESSION['email'] = $rememberUser['email'];
            $_SESSION['mdp'] = $rememberUser['mdp'];
            $_SESSION['pseudo'] = $rememberUser['pseudo'];
            $_SESSION['statut'] = $rememberUser['statut'];
            setcookie('user',$rememberUser['token'],time()+3600*24*3,'/','localhost',false,true);
        }else{header('Location: deconnexion.php');}
        
    }
?>