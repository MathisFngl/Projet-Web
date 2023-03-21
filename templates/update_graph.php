<?php
    session_start();
    require_once 'bdd.php';
    include_once('remember.php');

    if(isset($_GET['user'])){
        $requUser = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user WHERE token = ?');
        $token = $_GET['user'];
        $requUser->execute(array($token));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php?user='.$token);}

    function newCandle($bdd){
        $requValDernierMois = $bdd->prepare('SELECT prix,mois FROM historiqueaction WHERE mois = (SELECT MAX(mois) FROM historiqueaction)');
        $requValDernierMois->execute();
        $requValAvantDernierMois = $bdd->prepare('SELECT prix,mois FROM historiqueaction WHERE (mois = (SELECT MAX(mois) FROM historiqueaction)-1)');
        $requValAvantDernierMois->execute();

        $DerniereVal = $requValDernierMois->fetch();
        $AvantDerniereVal = $requValAvantDernierMois->fetch();

        $pourcentage_ecart_avant = (($DerniereVal['prix'] - $AvantDerniereVal['prix'])/$AvantDerniereVal['prix'])*100.0;
        $random = rand(-3, 3);
        $pourcentage_ecart = $pourcentage_ecart_avant + $random;

        if($pourcentage_ecart < -10){
            $pourcentage_final = -10;
        }
        if($pourcentage_ecart > 10){
            $pourcentage_final = 10;
        }

        $ID_Action = 0;
        $mois = $DerniereVal["mois"] + 1;
        $prix = $DerniereVal["prix"] + $DerniereVal["prix"]*($pourcentage_final/100);
        $ID_partie = 0;

        $newPriceToAdd = $bdd->prepare('INSERT INTO historiqueaction VALUES (?,?,?,?)');
        $newPriceToAdd->execute(array($ID_Action, $mois, $prix, $ID_partie));
    }

    
    function constructionTableau($bdd){
        $ID_Action = 0;
        $requHistoriquePrix = $bdd->prepare("SELECT prix, mois FROM historiqueaction WHERE ID_Action = ? BY mois DESC LIMIT 36");
        $requHistoriquePrix -> execute($ID_Action);
        $HistoriquePrix = $requHistoriquePrix->fetch();
        $dataTable = [];
        for($i = 1; $i<=35; $i++){
            $bougie = ["$i", $HistoriquePrix[$i-1]["prix"], $HistoriquePrix[$i-1]["prix"], $HistoriquePrix[$i]["prix"], $HistoriquePrix[$i]["prix"]];
            array_push($dataTable, $bougie);
        }
        return $dataTable;
    }
?>