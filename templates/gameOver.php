<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User,nbPartie FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}
    $reqSolde = $bdd->prepare('UPDATE user SET soldeJoueur = ?, nbPartie = ? WHERE ID_User = ?');
    $reqSolde->execute(array(10000.00,$dataUser['nbPartie']+1,$dataUser['ID_User']));
    $reqDelAction = $bdd->prepare('DELETE FROM actionpossede WHERE ID_User = ?');
    $reqDelAction->execute(array($dataUser['ID_User']));

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../static/style/style.css">
</head>
    <body>
        <h2>Vous avez perdu !</h2>
        <div>
        <form method="get">
            <button class="button-resume"><a href="profile.php">Recommencer</a></button>
            <button class="button-resume"><a href="deconnexion.php">Se déconnecter</a></button>
        </form>
        </div>
    </body>
</html>