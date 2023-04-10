<?php 
    session_start();
    require_once 'bdd.php';

    // Si les variables existent et qu'elles ne sont pas vides
    if(!empty($_POST['email']) && !empty($_POST['password']))
    {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $remember = isset($_POST['remember']);
        $email = strtolower($email);

        // On vérifie si l'utilisateur existe
        $requUser = $bdd->prepare('UPDATE user SET token = ? WHERE email = ?');
        $requUser->execute(array(bin2hex(openssl_random_pseudo_bytes(64)),$email));
        $verif = $bdd->prepare('SELECT pseudo, email, mdp,token FROM user WHERE email = ?');
        $verif->execute(array($email));
        $data = $verif->fetch();
        $userExist = $verif->rowCount();
        
        if($userExist > 0){ 
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
                if(password_verify($password,$data['mdp'])){
                    if($email == 'virtualtrader23@gmail.com'){
                        $_SESSION['user'] = $data['token'];
                        $_SESSION['statut'] = $data['statut'];
                        header('Location: admin.php');
                        die();
                    }
                    else{
                        if(isset($_POST['remember'])){
                            setcookie('user',$data['token'],time()+3600*24*3,'/','localhost',false,true);
                        }
                        $_SESSION['user'] = $data['token'];
                        $_SESSION['email'] = $data['email'];
                        $_SESSION['mdp'] = $data['mdp'];
                        $_SESSION['pseudo'] = $data['pseudo'];
                        $_SESSION['statut'] = $data['statut'];
                        header('Location: profile.php');
                        die();
                    }
                }else{ header('Location: login.php?reg_err=password'); die();}
            }else{ header('Location: login.php?reg_err=email'); die();}
        }else{ header('Location: login.php?reg_err=already'); die();}
    }else{ header('Location: login.php?reg_err=already'); die();}
