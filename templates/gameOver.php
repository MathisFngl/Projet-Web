<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User, pseudo, nbPartie FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}
    $reqSolde = $bdd->prepare('UPDATE user SET soldeJoueur = ?, nbPartie = ? WHERE ID_User = ?');
    $reqSolde->execute(array(10000.00,$dataUser['nbPartie']+1,$dataUser['ID_User']));
    $reqDelAction = $bdd->prepare('DELETE FROM actionpossede WHERE ID_User = ?');
    $reqDelAction->execute(array($dataUser['ID_User']));

    //Splash Screen aléatoire
    $file_path = '../static/splash.txt';
    $text = file_get_contents($file_path);
    $sentences = explode("\n", $text);
    $total_sentences = count($sentences);
    $random_index = rand(0, $total_sentences - 1);
    $random_sentence = $sentences[$random_index];

    //Score
    function Benefice($bdd, $ID_Action, $mois, $amount){
        $requPrix = $bdd->prepare('SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois = ?');
        $requPrix->execute(array($ID_Action, $mois));
        $ActionPrix = $requPrix->fetch();
        return $ActionPrix["prix"] * $amount;
    }

    $reqcount = $bdd->prepare('SELECT COUNT(*) FROM historiquetrade WHERE ID_User = ?');
    $reqcount -> execute(array($dataUser["ID_User"]));
    $lines = $reqcount->fetch();

    $req = $bdd->prepare('SELECT * FROM historiquetrade WHERE ID_User = ?');
    $req -> execute(array($dataUser["ID_User"]));
    $donnees = $req->fetch();
    $Somme_gagnee = 0;
    for($i=0; $i < $lines[0]; $i++){

        if($donnees["statut"] == 1) {
            $Somme_gagnee += Benefice($bdd, $donnees["ID_Action"], $donnees["mois"], $donnees["nombreAction"]);
        }
        else if($donnees["statut"] == 2){
            $Somme_gagnee += $donnees["nombreAction"];
        }
        $donnees = $req->fetch();
    }

    $reqDelAction = $bdd->prepare('DELETE FROM historiquetrade WHERE ID_User = ?');
    $reqDelAction->execute(array($dataUser['ID_User']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../static/style/gameover.css">
</head>
    <body>
        <h2>You Died!</h2>
        <div class="lower_box">
            <p class="splash"> <?php echo $dataUser["pseudo"] . $random_sentence?></p>
            <p class="score"> <?php echo "Score : <a style='color: #fffb00'>" . $Somme_gagnee . "</a>" ?> </p>
            <form method="get">
                <button class="button-resume"><a href="profile.php">Respawn</a></button><br>
                <button class="button-resume"><a href="deconnexion.php">Title Screen</a></button>
            </form>
        </div>
    </body>
</html>