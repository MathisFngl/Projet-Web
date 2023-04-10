<?php
    require_once 'bdd.php';

    function newCandle($bdd, $ID_Action){
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
            $pourcentage_ecart = -10;
        }
        if($pourcentage_ecart > 10){
            $pourcentage_ecart = 10;
        }
        $mois = $DerniereVal["mois"] + 1;
        $prix = $DerniereVal["prix"] + $DerniereVal["prix"]*($pourcentage_ecart/100);

        $newPriceToAdd = $bdd->prepare('INSERT INTO historiqueaction VALUES (?,?,?)');
        $newPriceToAdd->execute(array($ID_Action, $mois, $prix));
    }

    function constructionTableau($bdd, $data_amount){
        $ID_Action = 2;
        $requHistoriquePrix = $bdd->prepare("SELECT prix, mois FROM historiqueaction WHERE ID_Action = ? ORDER BY mois DESC LIMIT 10");
        $requHistoriquePrix -> execute(array($ID_Action));
        $HistoriquePrix = [];
        $curr_output = $requHistoriquePrix->fetch();
        while($curr_output != null){
            $curr_output = $requHistoriquePrix->fetch();
            array_push($HistoriquePrix, $curr_output);
        }

        $dataTable = [];
        for($i = 1; $i<=6; $i++){
            $bougie = ["$i", $HistoriquePrix[$i]["prix"], $HistoriquePrix[$i]["prix"], $HistoriquePrix[$i-1]["prix"], $HistoriquePrix[$i-1]["prix"]];
            array_push($dataTable, $bougie);
        }

        $dataTable = array_reverse($dataTable);
        return $dataTable;
    }