<?php
    if(isset($_COOKIE['user']) AND !empty($_COOKIE['user'])){
        setcookie('user',$data['id'],time() + 3600*24*2, '/', 'localhost', false, true);
        $verif = $bdd->prepare('SELECT pseudo, email, mdp,token FROM user WHERE id = ?');
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
        header('Location: profile.php');
    }
?>