<?php 
    session_start();
    require_once 'bdd.php';

    // Si les variables existent et qu'elles ne sont pas vides
    if(!empty($_POST['email']) && !empty($_POST['password']))
    {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        $email = strtolower($email);

        // On vérifie si l'utilisateur existe
        $verif = $bdd->prepare('SELECT pseudo, email, mdp,token FROM user WHERE email = ?');
        $verif->execute(array($email));
        $data = $verif->fetch();
        $userExist = $verif->rowCount();
        
        if($userExist > 0){ 
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){ 
                if(password_verify($password,$data['mdp'])){
                    $_SESSION['user'] = $data['token'];
                    header('Location: voir_stocks.php');
                    die();
                }else{ header('Location: login.php?reg_err=password'); die();}
            }else{ header('Location: login.php?reg_err=email'); die();}
        }else{ header('Location: login.php?reg_err=already'); die();}
    }else{ header('Location: login.php?reg_err=already'); die();}
