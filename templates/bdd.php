<?php
    try
    {
        $bdd = new PDO('mysql:host=localhost;dbname=virtual_trader;charset=utf8', 'root', '');
    }catch(Exception $e)
    {
         die('Erreur : '.$e->getMessage());
    }