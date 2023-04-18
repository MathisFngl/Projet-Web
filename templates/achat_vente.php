<?php
    session_start();
    require_once 'bdd.php';
    require_once 'update_graph.php';

    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user WHERE token = ?');
        $token = $_SESSION['user'];
        $requUser->execute(array($token));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    $prix = $_POST['unit-price'];
    $ID_Action = $_POST['ID_Action'];

    if (isset($_POST['buy'])) {
        $numberInput = $_POST['numberInput'];
        $moneyCalc = $numberInput*$prix;
        if($moneyCalc <= $dataUser['soldeJoueur']){
            $new_solde_joueur = $dataUser["soldeJoueur"] - $moneyCalc;
            $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE token = ?");
            $sql_money_update->execute(array($new_solde_joueur, $_SESSION['user']));
            if($numberInput != 0){
                $sql_action_possede = $bdd->prepare("INSERT INTO actionpossede(ID_Action,ID_User, nombreAction, prix_achat) VALUES (?,?,?,?)");
                $sql_action_possede->execute(array($ID_Action, $dataUser['ID_User'], $numberInput, $prix));
            }
            $dataUser['soldeJoueur'] = $new_solde_joueur;
        }
    } else if (isset($_POST['sell'])) {
        $stockAmount = $bdd->prepare("SELECT SUM(nombreAction) FROM actionpossede WHERE ID_Action = ? AND ID_User = ?");
        $stockAmount -> execute(array($ID_Action, $dataUser['ID_User']));
        $max_sell_amount = $stockAmount -> fetch();

        $Actions = $bdd->prepare("SELECT nombreAction, ID_achat FROM actionpossede WHERE ID_Action = ? AND ID_User = ?");
        $Actions -> execute(array($ID_Action, $dataUser['ID_User']));
        $ActionToSubtract = $Actions -> fetch();

        if($max_sell_amount[0] >= $_POST['numberInput']){
            $amount_to_subtract = $_POST['numberInput'];
            while($amount_to_subtract > 0){
                if(($ActionToSubtract["nombreAction"] <= $amount_to_subtract) && isset($ActionToSubtract["nombreAction"])){

                    $amount_to_subtract = $amount_to_subtract - $ActionToSubtract["nombreAction"];
                    $Delete = $bdd->prepare("DELETE FROM actionpossede WHERE ID_achat = ?");
                    $Delete -> execute(array($ActionToSubtract["ID_achat"]));
                    var_dump($amount_to_subtract);
                }else{
                    $Update = $bdd->prepare("UPDATE actionpossede SET nombreAction = ? WHERE ID_achat = ?");
                    $Update -> execute(array($ActionToSubtract["nombreAction"] - $amount_to_subtract, $ActionToSubtract["ID_achat"]));
                    $amount_to_subtract = 0;
                    var_dump($amount_to_subtract);
                }
                $ActionToSubtract = $Actions -> fetch();
            }

            $numberInput = $_POST['numberInput'];
            $moneyCalc = $numberInput*$prix;

            $new_solde_joueur = $dataUser["soldeJoueur"] + $moneyCalc;
            $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE token = ?");
            $sql_money_update->execute(array($new_solde_joueur, $_SESSION['user']));
        }
    }

header("Location: voir_stocks.php?ID_Action=" .$_POST["ID_Action"]);