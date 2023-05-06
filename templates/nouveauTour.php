<?php
    require_once 'bdd.php';
    require_once 'update_graph.php';
    require_once 'calculTotalArgent.php';

    //Récupère le timestamp de la dernière actualisation (le temps maximal de la table donc)
    $timeReq = $bdd->prepare("SELECT MAX(timestamp_nb) FROM gestiontour");
    $timeReq->execute();
    $lastTime = $timeReq->fetch();
    $current_timestamp = time();

    if ($current_timestamp - $lastTime[0] >= 110) {
        //S'il s'est déroulé au moins 110 secondes avant la dernères actualisation
        //Si la condition est respectée, un nouveau tour est déclaré.
        nouveauTour($bdd);
        $newTime = $bdd->prepare("INSERT INTO  gestiontour(timestamp_nb) VALUES (?)");
        $newTime->execute(array($current_timestamp));
    }
function nouveauTour($bdd)
{
    //Récupère toute les informations pour chaque stock
    $stocksReq = $bdd->prepare("SELECT * FROM dataaction");
    $stocksReq->execute();
    $stock = $stocksReq->fetch();

    //Récupère le nombre de stock de la table
    $nbStockReq = $bdd->prepare("SELECT COUNT(*) FROM dataaction");
    $nbStockReq->execute();
    $nbstock = $nbStockReq->fetch();

    //Actualise les stocks en créant une nouvelle bougie pour chacun d'eux
    for ($z=0; $z<$nbstock[0]; $z++) {
        $id = $stock["ID_Action"];
        newCandle($bdd, $id);
        $stock = $stocksReq->fetch();
    }

    //Récupère le mois courant
    $MoisReq = $bdd->prepare("SELECT MAX(mois) FROM historiqueaction;");
    $MoisReq->execute();
    $Mois = $MoisReq->fetch();
    $nouveauMois = $Mois[0] + 1;

   
    // GESTION DES DIVIDENDES

    //Récupère les données des utilisateurs présents dans la table
    $requUser = $bdd->prepare('SELECT soldeJoueur, ID_User FROM user');
    $requUser->execute();
    $dataUser = $requUser->fetch();

    //Récupère le nombre d'utilisateurs
    $nbUserreq = $bdd->prepare('SELECT COUNT(*) FROM user');
    $nbUserreq->execute();
    $nbUser = $nbUserreq->fetch();
    $nbUser = $nbUser[0];

    for ($i = 0; $i < $nbUser; $i++) {

        if ($nouveauMois % 12 == 0) { // Tous les 12 mois

            $sommeDividendes = 0;

            //prendre l'ID et le nombre de stock que possède un User
            $DivAc = $bdd->prepare("SELECT ID_Action, nombreAction FROM actionpossede WHERE ID_User = ?;");
            $DivAc->execute(array($dataUser["ID_User"]));
            $ActionDiv = $DivAc->fetch();

            //Compte le nombre de lignes de stocks présente dans actionpossede pour un certain utilisateur
            $DivCount = $bdd->prepare("SELECT COUNT(*) FROM actionpossede WHERE ID_User = ?;");
            $DivCount->execute(array($dataUser["ID_User"]));
            $nbActions = $DivCount->fetch();

            //Boucler pour traiter toute les actions de l'utilisateur
            for ($j = 0; $j < $nbActions[0]; $j++) {

                //Prend le prix du dernier mois
                $PrixAc = $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois = ?;");
                $PrixAc->execute(array($ActionDiv["ID_Action"], $Mois[0]-1));
                $ActionPrix = $PrixAc->fetch();

                //Récupère la valeur du dividende (un %) pour une action
                $Div = $bdd->prepare("SELECT dividende FROM dataaction WHERE ID_Action = ?;");
                $Div->execute(array($ActionDiv["ID_Action"]));
                $dividende = $Div->fetch();

                //Ajoute la valeur des dividendes gagnés et prend la prochaine ligne de actionpossede pour l'Utilisateur
                $sommeDividendes = $sommeDividendes + ($dividende[0]/100 * $ActionPrix[0] * $ActionDiv["nombreAction"]);
                $ActionDiv = $DivAc->fetch();
            }
            //Met a jour l'argent du joeur
            $new_solde_joueur = $dataUser["soldeJoueur"] + $sommeDividendes;
            $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE ID_User = ?");
            $sql_money_update -> execute(array($new_solde_joueur, $dataUser["ID_User"]));

            //Ajoute une ligne dans l'historique indiquant la somme totale gagnée en dividende
            $sql_historique_trade = $bdd->prepare("INSERT INTO historiquetrade(ID_User,ID_Action, nombreAction, statut ,mois) VALUES (?,1,?,2,?)");
            $sql_historique_trade -> execute(array($dataUser['ID_User'], $sommeDividendes, $Mois[0]));
        }

        // Ajoute une ligne dans l'historique du portefeuille
        $sql_historique_portefeuille = $bdd->prepare("INSERT INTO historiqueportefeuille(ID_User, mois, solde) VALUES (?,?,?)");
        $sql_historique_portefeuille -> execute(array($dataUser['ID_User'], $Mois[0], ArgentTotal($bdd, $dataUser['ID_User'])));

         //GESTION EMPRUNT
         // récupération de tout les emprunts d'un joueur
        $reqEmprunt = $bdd->prepare('SELECT moisEmprunt,ID_Emprunt,valeurEmprunt FROM emprunt WHERE ID_User=?');
        $reqEmprunt->execute(array($dataUser['ID_User']));
        $emprunt = $reqEmprunt->fetchAll();
        $nbEmprunt = $reqEmprunt->rowCount();
        $prelSolde = 0;

        // calcul du solde total à prélever
        for($k=0;$k<$nbEmprunt;$k++){
        $prelSolde = $prelSolde + $emprunt[$k]['valeurEmprunt'];
        // si mois =1 donc le derniier mois il n'y aura plus d'emprunt donc un supprime
        if($emprunt[$k]['moisEmprunt'] == 1){
            $reqmoisEmprunt = $bdd->prepare('DELETE FROM emprunt WHERE ID_Emprunt = ?');
            $reqmoisEmprunt -> execute(array($emprunt[$k]['ID_Emprunt']));
        }
        //sinon on décrémente de 1 le nombre de mois
        else{
            $reqmoisEmprunt = $bdd->prepare('UPDATE  emprunt SET moisEmprunt = ? WHERE ID_Emprunt = ?');
            $reqmoisEmprunt -> execute(array($emprunt[$k]['moisEmprunt']-1,$emprunt[$k]['ID_Emprunt']));
        }
    }

    // ajout à l'historique
    if($prelSolde != 0){
        $sql_historique_emprunt = $bdd->prepare("INSERT INTO historiquetrade(ID_User,ID_Action, nombreAction, statut ,mois) VALUES (?,1,?,4,?)");
        $sql_historique_emprunt->execute(array($dataUser['ID_User'], $prelSolde, $Mois[0]));
    }

    // modification du solde du joueur
    $reqSoldeEmprunt = $bdd->prepare('UPDATE user SET soldeJoueur = ? WHERE ID_User = ?');
    $reqSoldeEmprunt->execute(array($dataUser['soldeJoueur']-$prelSolde,$dataUser['ID_User']));
    $dataUser = $requUser->fetch();
    }
    //GESTION GAME OVER
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User,soldeJoueur FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }

    if(ArgentTotal($bdd, $dataUser["ID_User"]) < 1000){
        header("Location: gameOver.php");
    }
    header("Location: voir_stocks.php?ID_Action=1");
}