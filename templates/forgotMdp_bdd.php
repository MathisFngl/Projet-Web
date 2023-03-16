<?php
    require_once 'bdd.php';
    if(isset($_GET['section'])){
        $section = htmlspecialchars($_GET['section']);
    }else{ $section="";}
    if(isset($_POST['email'])){
        if(!empty($_POST['email'])){
            $email = htmlspecialchars($_POST['email']);
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $verifMail = $bdd->prepare('SELECT ID_User FROM user WHERE email = ?');
                $verifMail->execute(array($email));
                $mailExist = $verifMail->rowCount();

                if($mailExist == 1){
                    $_SESSION['email'] = $email;
                    for($i=0; $i < 9; $i++){
                        $code .= rand(0,9);
                    }
                    $mailIn = $bdd->prepare('SELECT ID_Forgot FROM forgotMdp WHERE email = ?');
                    $mailIn->execute(array($email));
                    $mailIn = $mailIn->rowCount();
                    if($mailIn == 1){
                        $forgot_insert = $bdd->prepare('UPDATE forgotMdp SET code=? WHERE email=? ');
                        $forgot_insert->execute(array($code,$email));
                    }else{
                        $forgot_insert = $bdd->prepare('INSERT INTO forgotMdp(email,code) VALUES (?,?)');
                        $forgot_insert->execute(array($email,$code));
                    }
                    $to = 'antoinemacmil45@gmail.com';
                    $subject = 'the subject';
                    $message = 'hello';
                    $headers = array(
                    'From' => 'antoinemacmil45@gmail.com',
                    'Reply-To' => 'antoinemacmil45@gmail.com',
                    'X-Mailer' => 'PHP/' . phpversion()
                    );

                    mail($to, $subject, $message, $headers);
                    $message = "Bonjour, voici le code afin de modifier votre mot de passe : $code";
                    $header = 'Content-Type: text/plain; charset="utf-8"'."";

                    if(mail($email, "Changement de mot de passe",$message,$header)){
                        header("Location: forgotMdp.php");
                    }//else{ header('Location: forgotMdp.php?reg_err=email'); die();}
                }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
            }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
        }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
    }else{ header('Location: fortgotMdp.php?reg_err=email'); die();}

    if(isset($_POST['codeSubmit'])){
        $codeSubmit = htmlspecialchars($_POST['codeSubmit']);
        if(!empty($codeSubmit)){
            $verifCode = $bdd->prepare('SELECT ID_Forgot FROM forgotMdp WHERE email = ? AND code=?');
            $verifCode->execute(array($_SESSION['email'],$codeSubmit));
            $codeExist = $verifMail->rowCount();
            if($codeExist == 1){
                $delCode = $bdd->prepare('DELETE FROM forgotMdp WHERE mail=?');
                $delCode->execute(array($_SESSION['email']));
                header("Location: forgotMdp.php?section=resetMdp");

            }else{ header('Location: forgotMdp.php?reg_err=code'); die();}
        }else{ header('Location: forgotMdp.php?reg_err=code'); die();}
    }
    if(isset($_POST['newPassword'],$_POST['newPassword2'])){
        $newPassword = htmlspecialchars($_POST['newPassword']);
        $newPassword2 = htmlspecialchars($_POST['newPassword2']);
        $enterCode = $bdd->prepare('SELECT codeOk FROM forgotMdp WHERE email = ?');
        $enterCode->execute(array($_SESSION['email']));
        $enterCode->fetch();
        $enterCode = $enterCode['codeOK'];
        if($enterCode == 1){
            if(!empty($newPassword) && !empty($newPassword2)){
                if($newPassword === $newPassword2){
                    $upCodeOk = $bdd->prepare('UPDATE forgotMdp SET codeOk=? WHERE email=? ');
                    $upCodeOk->execute(array(1,$_SESSION['email']));
                    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $password_update = $bdd->prepare('UPDATE user SET mdp=? WHERE email=? ');
                    $password_update->execute(array($code,$_SESSION['email']));
                    header("Location: login.php");
                }else{ header('Location: forgotMdp.php?reg_err=password'); die();}
            }else{ header('Location: forgotMdp.php?reg_err=password'); die();}
        }else{ header('Location: forgotMdp.php?reg_err=password'); die();}
    }else{ header('Location: forgotMdp.php?reg_err=password'); die();}
  
?>