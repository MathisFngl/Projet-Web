<?php

    require_once 'bdd.php';
    require_once 'update_graph.php';
    require_once 'calculTotalArgent.php';

    $timeReq = $bdd->prepare("SELECT MAX(timestamp_nb) FROM gestiontour");
    $timeReq->execute();
    $lastTime = $timeReq->fetch();
    $current_timestamp = time();

    if ($current_timestamp - $lastTime[0] >= 110) {
        nouveauTour($bdd);
        $newTime = $bdd->prepare("INSERT INTO  gestiontour(timestamp_nb) VALUES (?)");
        $newTime->execute(array($current_timestamp));
    }
function nouveauTour($bdd)
{
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User,soldeJoueur FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    $stocksReq = $bdd->prepare("SELECT * FROM dataaction");
    $stocksReq->execute();
    $stock = $stocksReq->fetch();

    while ($stock["ID_Action"] != null) {
        $id = $stock["ID_Action"];
        newCandle($bdd, $id);
        $stock = $stocksReq->fetch();
    }

    $MoisReq = $bdd->prepare("SELECT MAX(mois) FROM historiqueaction;");
    $MoisReq->execute();
    $Mois = $MoisReq->fetch();
    $nouveauMois = $Mois[0] + 1;

    //GESTION EMPRUNT
    $reqEmprunt = $bdd->prepare('SELECT moisEmprunt,soldeEmprunt,ID_Emprunt FROM emprunt WHERE ID_User=?');
    $reqEmprunt->execute(array($dataUser['ID_User']));
    $emprunt = $reqEmprunt->fetchAll();
    $nbEmprunt = $reqEmprunt->rowCount();
    $prelSolde = 0;

    for($i=0;$i<$nbEmprunt;$i++){
        $prelSolde = $prelSolde + ($emprunt[$i]['soldeEmprunt']/$emprunt[$i]['moisEmprunt']);
        // si mois =1 donc le derniier mois il n'y aura plus d'emprunt donc un supprime
        if($emprunt[$i]['moisEmprunt'] == 1){
            $reqmoisEmprunt = $bdd->prepare('DELETE FROM emprunt WHERE ID_Emprunt = ?');
            $reqmoisEmprunt -> execute(array($emprunt[$i]['ID_Emprunt']));
        }
        else{
            $reqmoisEmprunt = $bdd->prepare('UPDATE  emprunt SET moiEmprunt = ? WHERE ID_Emprunt = ?');
            $reqmoisEmprunt -> execute(array($emprunt[$i]['moisEmprunt']-1,$emprunt[$i]['ID_Emprunt']));
        }
    }
    echo $prelSolde;

    if($prelSolde != 0){
        $sql_historique_emprunt = $bdd->prepare("INSERT INTO historiquetrade(ID_User,ID_Action, nombreAction, statut ,mois) VALUES (?,1,?,4,?)");
        $sql_historique_emprunt->execute(array($dataUser['ID_User'], $prelSolde, $Mois[0]));
    }

    $reqSoldeEmprunt = $bdd->prepare('UPDATE user SET soldeJoueur = ? WHERE ID_User = ?');
    $reqSoldeEmprunt->execute(array($dataUser['soldeJoueur']-$prelSolde,$dataUser['ID_User']));
    

    // GESTION DES DIVIDENDES

    $requUser = $bdd->prepare('SELECT soldeJoueur, ID_User FROM user');
    $requUser->execute();
    $dataUser = $requUser->fetch();

    $nbUserreq = $bdd->prepare('SELECT COUNT(*) FROM user');
    $nbUserreq->execute();
    $nbUser = $nbUserreq->fetch();
    $nbUser = $nbUser[0];

    for ($i = 0; $i < $nbUser; $i++) {

        $sql_historique_portefeuille = $bdd->prepare("INSERT INTO historiqueportefeuille(ID_User, mois, solde) VALUES (?,?,?)");
        $sql_historique_portefeuille -> execute(array($dataUser['ID_User'], $Mois[0], ArgentTotal($bdd, $dataUser['ID_User'])));

        if ($nouveauMois % 12 == 0) {
            $sommeDividendes = 0;
            $DivAc = $bdd->prepare("SELECT ID_Action, nombreAction FROM actionpossede WHERE ID_User = ?;");
            $DivAc->execute(array($dataUser["ID_User"], $Mois[0]));
            $ActionDiv = $DivAc->fetch();

            $DivCount = $bdd->prepare("SELECT COUNT(*) FROM actionpossede WHERE ID_User = ?;");
            $DivCount->execute(array($dataUser["ID_User"]));
            $nbActions = $DivCount->fetch();

            for ($j = 0; $j < $nbActions[0]; $j++) {

                $PrixAc = $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois = ?;");
                $PrixAc->execute(array($ActionDiv["ID_Action"], $Mois[0]-1));
                $ActionPrix = $PrixAc->fetch();

                $Div = $bdd->prepare("SELECT dividende FROM dataaction WHERE ID_Action = ?;");
                $Div->execute(array($ActionDiv["ID_Action"]));
                $dividende = $Div->fetch();

                $sommeDividendes = $sommeDividendes + ($dividende[0]/100 * $ActionPrix[0] * $ActionDiv["nombreAction"]);
                $ActionDiv = $DivAc->fetch();
            }
            $new_solde_joueur = $dataUser["soldeJoueur"] + $sommeDividendes;
            $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE ID_User = ?");
            $sql_money_update -> execute(array($new_solde_joueur, $dataUser["ID_User"]));

            $sql_historique_trade = $bdd->prepare("INSERT INTO historiquetrade(ID_User,ID_Action, nombreAction, statut ,mois) VALUES (?,1,?,2,?)");
            $sql_historique_trade -> execute(array($dataUser['ID_User'], $sommeDividendes, $Mois[0]));
        }
        //GESTION GAME OVER
        if(ArgentTotal($bdd, $dataUser["ID_User"]) < 1000){
            header("Location: gameOver.php");
        }
        $dataUser = $requUser->fetch();
    }

    header("Location: voir_stocks.php?ID_Action=" . $_POST["ID_Action"]);
}