<?php
    session_start();
    require_once 'bdd.php';
    require_once 'update_graph.php';

    $stocksReq = $bdd->prepare("SELECT * FROM dataaction");
    $stocksReq -> execute();
    $stock = $stocksReq->fetch();

    while($stock["ID_Action"] != null){
        $id = $stock["ID_Action"];
        newCandle($bdd,$id);
        $stock = $stocksReq->fetch();
    }

    header("Location: voir_stocks.php");