<?php 
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User,soldeJoueur FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    if(!empty($_POST['soldeEmprunt']) && !empty($_POST['moisEmprunt'])){
        $solde = $_POST['soldeEmprunt'];
        $mois = $_POST['moisEmprunt'];
        if($mois<24){
            if($solde/$mois < $dataUser['soldeJoueur']){
                $reqEmprunt = $bdd->prepare('INSERT INTO emprunt(ID_User,moisEmprunt,soldeEmprunt) VALUES(?,?,?)');
                $reqEmprunt->execute(array($dataUser['ID_User'],$mois,$solde));
                $reqSoldeEmprunt = $bdd->prepare('UPDATE user SET soldeJoueur = ? WHERE ID_User = ?');
                $reqSoldeEmprunt->execute(array($dataUser['soldeJoueur']+$solde,$dataUser['ID_User']));
                header('Location:profile.php');
            }
        }
    }
?>