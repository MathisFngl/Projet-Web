<?php
    require_once 'bdd.php';

function Benefice($bdd, $ID_Action, $mois, $amount){
    $requPrix = $bdd->prepare('SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois = ?');
    $requPrix->execute(array($ID_Action, $mois));
    $ActionPrix = $requPrix->fetch();
    return $ActionPrix["prix"] * $amount;
}
function ArgentTotal($bdd, $ID_User){

    $requUser = $bdd->prepare('SELECT soldeJoueur FROM user WHERE ID_User = ?');
    $requUser->execute(array($ID_User));
    $dataUser = $requUser->fetch(); // Prend les données du joueur

    $Total = $dataUser["soldeJoueur"];

    $MoisReq = $bdd->prepare("SELECT MAX(mois) FROM historiqueaction;");
    $MoisReq->execute();
    $Mois = $MoisReq->fetch(); // Prend le dernier mois

    $ActCount = $bdd->prepare("SELECT COUNT(*) FROM actionpossede WHERE ID_User = ?;");
    $ActCount->execute(array($ID_User));
    $nbAct = $ActCount->fetch();
    $nbAct = $nbAct[0]; // Nombre d'actions

    $Div = $bdd->prepare("SELECT ID_Action, nombreAction FROM actionpossede WHERE ID_User = ?;");
    $Div->execute(array($ID_User));
    $ActDiv = $Div->fetch(); // Demande quelles sont les actions que le joueur possède.

    for ($k = 0; $k < $nbAct; $k++) {
        $Total += Benefice($bdd, $ActDiv["ID_Action"], $Mois[0]-1, $ActDiv["nombreAction"]);
        $ActDiv = $Div->fetch();
    }

    return $Total;
}