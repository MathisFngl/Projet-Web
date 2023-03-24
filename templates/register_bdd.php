<?php 
    require_once 'bdd.php';

    // Si les variables existent et qu'elles ne sont pas vides
    if(!empty($_POST['pseudo']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['photo']))
    {
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);
        $password2 = htmlspecialchars($_POST['password2']);
        $photo = htmlspecialchars($_POST['photo']);

        // On vérifie si l'utilisateur existe
        $verif = $bdd->prepare('SELECT pseudo, email, mdp, token FROM user WHERE email = ?');
        $verif->execute(array($email));
        $data = $verif->fetch();
        $userExist = $verif->rowCount();

        $email = strtolower($email);
        
        if($userExist== 0){ 
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                if(strlen($pseudo) <= 100){
                    if(strlen($email) <= 255){
                        if($password === $password2){
                            $password = password_hash($password, PASSWORD_DEFAULT);
                            $soldeJoueur = 10000.00;
                            $insert = $bdd->prepare('INSERT INTO user(pseudo, email, mdp,token,soldeJoueur,photo) VALUES(?, ?, ?,?,?,?)');
                            $insert->execute(array($pseudo,$email,$password,bin2hex(openssl_random_pseudo_bytes(64)),$soldeJoueur,$photo));
                            header('Location:login.php');
                            die();
                        }else{ header('Location: register.php?reg_err=password'); die();}
                    }else{ header('Location: register.php?reg_err=email'); die();}
                }else{ header('Location: register.php?reg_err=pseudo'); die();}
            }else{ header('Location: register.php?reg_err=email'); die();}
        }else{ header('Location: register.php'); die();}
    }else{ header('Location: register.php'); die();}
