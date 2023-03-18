<?php
    use PHPMailer\PHPMailer\PHPMailer;

    require_once 'bdd.php';
    require_once '../PHPMailer/PHPMailer.php';
    require_once '../PHPMailer/SMTP.php';
    require_once '../PHPMailer/Exception.php';
    if(isset($_GET['section'])){
        $section = htmlspecialchars($_GET['section']);
    }else{ $section="";}
    if(isset($_POST['email'])){
        if(!empty($_POST['email'])){
            $email = htmlspecialchars($_POST['email']);
            if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                $code = rand(10000000,99999999);
                echo $code;
                $verifMail = $bdd->prepare('SELECT ID_User FROM user WHERE email = ?');
                $verifMail->execute(array($email));
                $mailExist = $verifMail->rowCount();

                if($mailExist == 1){
                    $_SESSION['email'] = $email;
                    
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
                    $subject = "Code de rénitialisation";
                    $message = "Bonjour, voici le code pour rénitialiser votre mot de passe".$code;

                    $mail = new PHPMailer();
                    //SMTP settings
                    $mail->isSMTP();
                    $mail->Host= 'smtp.gmail.com';
                    $mail->SMTPAuth = true; 
                    $mail->Username = "virtualtrader23@gmail.com";
                    $mail->Password = "Capybara26!";
                    $mail->Port = 587;
                    $mail->SMTPSecure = "tls";

                    //settings email
                    $mail->CharSet = 'UTF-8';
                    $mail->From= "virtualtrader23@gmail.com";
                    $mail->FromName = "VirtualTrader";
                    $mail->addAddress($email);
                    $mail->Subject = $subject;
                    $mail->AltBody = $message;
                    $mail->isHTML(false);
                    $mail->msgHTML($message);

                    if($mail->send()){
                        echo "email envoyé";
                        header('Location: forgotMdp.php?section=code');
                    }else{ echo "email NON envoyé" .$mail->ErrorInfo;}
                }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
            }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
        }else{ header('Location: forgotMdp.php?reg_err=email'); die();}
    }else{ header('Location: fortgotMdp.php?reg_err=email'); die();}

    /*if(isset($_POST['codeSubmit'])){
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
        $codeExist = $enterCode->rowCount();
        if($codeExist == 1){
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
    }else{ header('Location: forgotMdp.php?reg_err=password'); die();}*/
  
?>