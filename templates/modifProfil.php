<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT email,pseudo FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    if(!empty($_POST)){
        if(isset($_POST['pseudoModif'])){
            if(!empty($_POST['pseudoModif'])){
                $pseudo = htmlspecialchars($_POST['pseudo']);
                if(strlen($pseudo) < 100){
                    $pseudoUpdate = $bdd->prepare('UPDATE user SET pseudo=? WHERE token=? ');
                    $pseudoUpdate->execute(array($pseudo,$_SESSION['user']));
                    header('Location: profile.php');
                }else{ echo "pseudo trop long";}
            }else{echo "pseudo vide";}
        }
        else if(isset($_POST['mailModif'])){
            if(!empty($_POST['mailModif'])){
                $email = htmlspecialchars($_POST['email']);
                $email = strtolower($email);
                // On vérifie si l'utilisateur existe
                $verif = $bdd->prepare('SELECT email FROM user WHERE email = ?');
                $verif->execute(array($email));
                $userExist = $verif->rowCount();
                if($userExist == 0){
                    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                        if(strlen($email) < 255){
                            $emailUpdate = $bdd->prepare('UPDATE user SET email=? WHERE token=? ');
                            $emailUpdate->execute(array($email,$_SESSION['user']));
                            header('Location: profile.php');
                        }else{echo "email trop grand";}
                    }else{echo "email non valide";}
                }else{echo "email deja pris";}
            }else{echo "email vide";}    
        }
        else if(isset($_POST['passwordModif'])){
            if(!empty($_POST['password']) && !empty($_POST['password2'])){
                $password = htmlspecialchars($_POST['password']);
                $password2 = htmlspecialchars($_POST['password2']);

                if($password == $password2){
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $passwordUpdate = $bdd->prepare('UPDATE user SET mdp=? WHERE token=? ');
                    $passwordUpdate->execute(array($password,$_SESSION['user']));
                    header('Location: profile.php');
                }else{ echo "mdp différent";}
            }else{echo "mdp vide";}
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="../static/style/style.css">
</head>
    <body>
    <nav class="navbar menu-padding-50">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" class="icon">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 11.25L10.25 5.75"></path>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.75 19.2502H6.25C6.80229 19.2502 7.25 18.8025 7.25 18.2502V15.75C7.25 15.1977 6.80229 14.75 6.25 14.75H5.75C5.19772 14.75 4.75 15.1977 4.75 15.75V18.2502C4.75 18.8025 5.19772 19.2502 5.75 19.2502Z"></path>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.75 19.2502H12.25C12.8023 19.2502 13.25 18.8025 13.25 18.2502V12.75C13.25 12.1977 12.8023 11.75 12.25 11.75H11.75C11.1977 11.75 10.75 12.1977 10.75 12.75V18.2502C10.75 18.8025 11.1977 19.2502 11.75 19.2502Z"></path>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.75 19.2502H18.25C18.8023 19.2502 19.25 18.8025 19.25 18.2502V5.75C19.25 5.19772 18.8023 4.75 18.25 4.75H17.75C17.1977 4.75 16.75 5.19772 16.75 5.75V18.2502C16.75 18.8025 17.1977 19.2502 17.75 19.2502Z"></path>
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 8.25V4.75H7.75"></path>
              </svg>
              <a href="#" class="logo">Virtual Trader</a>
            <div class="nav-links">
                <ul>
                    <li><a href="voir_stocks.php">Voir les stocks</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="#">Historique</a></li>
                    <li><a href="#">Amis</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <div class="modifUser">
            <h2 class="h2-modif">Modifier mon profil</h2>
            <div class="inputModif">
                <form method="post">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="pseudo" value="<?php echo $dataUser['pseudo'] ?>" class="modif">
                    <input type="submit" class="buttonModif" value="Modifier" name ="pseudoModif">
                </form>
            </div>
            <div class="inputModif">
                <form method="post">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" value="<?php echo $dataUser['email'] ?>" class="modif">
                    <input type="submit" class="buttonModif" value="Modifier" name ="emailModif">
                </form>
            </div>
            <div class="inputModif">
                <form method="post">
                    <div class="modifPassword1">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password" placeholder="Nouveau mot de passe" class="modif">
                    </div>
                    <div class="modifPassword2">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" name="password2" placeholder="Confirmation mot de passe" class="modif">
                    </div>
                    <input type="submit" class="buttonModifP" value="Modifier" name="passwordModif">
                </form>
            </div>
        </div>
            
    <script src="../js/app.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>