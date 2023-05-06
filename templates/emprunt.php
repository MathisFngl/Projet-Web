<?php 
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    //  on recupère les informations d'un joueur via son token + on vérifie s'il est connecté
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User,soldeJoueur FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}
    // si la balise form renvoie quelque chose
    if(!empty($_POST['soldeEmprunt']) && !empty($_POST['moisEmprunt'])){
        $solde = $_POST['soldeEmprunt'];
        $mois = $_POST['moisEmprunt'];
        $valeurEmprunt = ($solde/$mois)+(($solde/$mois)*0.1); 
        if($mois<24){
            // vérification qu'il peut payer
            if($valeurEmprunt < $dataUser['soldeJoueur']){
                $reqEmprunt = $bdd->prepare('INSERT INTO emprunt(ID_User,moisEmprunt,soldeEmprunt,valeurEmprunt) VALUES(?,?,?,?)');
                $reqEmprunt->execute(array($dataUser['ID_User'],$mois,$solde,$valeurEmprunt));

                // ajout à l'historique
                $MoisReq = $bdd->prepare("SELECT MAX(mois) FROM historiqueaction;");
                $MoisReq->execute();
                $Mois = $MoisReq->fetch();

                $sql_historique_emprunt = $bdd->prepare("INSERT INTO historiquetrade(ID_User,ID_Action, nombreAction, statut ,mois) VALUES (?,1,?,3,?)");
                $sql_historique_emprunt->execute(array($dataUser['ID_User'], $solde, $Mois[0]));

                //augmentation solde joueur
                $reqSoldeEmprunt = $bdd->prepare('UPDATE user SET soldeJoueur = ? WHERE ID_User = ?');
                $reqSoldeEmprunt->execute(array($dataUser['soldeJoueur']+$solde,$dataUser['ID_User']));
                header('Location:profile.php');
            }else{header('Location:profile.php?emprunt=1');}
        }
    }
?>