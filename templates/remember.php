<?php
    if(isset($_SESSION['user']) AND isset($_COOKIE['token']) AND !empty($_COOKIE['token'])){
        setcookie('user',$data['token'],time() + 3600*24*2, '/', 'localhost', false, true);
        $verif = $bdd->prepare('SELECT pseudo, email, mdp,token FROM user WHERE token = ?');
        $verif->execute(array($_COOKIE['user']));
        $data = $verif->fetch();
        $userExist = $verif->rowCount();

        if($userExist > 0){
            $_SESSION['user'] = $data['token'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['mdp'] = $data['mdp'];
            $_SESSION['pseudo'] = $data['pseudo'];
            $_SESSION['statut'] = $data['statut'];
        
        }
    }
?>