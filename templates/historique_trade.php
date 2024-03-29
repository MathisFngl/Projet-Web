<?php
session_start();
require_once 'bdd.php';
require('remember.php');
require 'nouveauTour.php';
require_once 'calculTotalArgent.php';
if(isset($_SESSION['user'])){
    $requUser = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user WHERE token = ?');
    $requUser->execute(array($_SESSION['user']));
    $dataUser = $requUser->fetch();
}else{header('Location: deconnexion.php');}

function ActionParser($bdd, $ID_Action)
{
    $requNom = $bdd->prepare('SELECT nomAction FROM dataaction WHERE ID_Action = ?');
    $requNom->execute(array($ID_Action));
    $ActionName = $requNom->fetch();
    return $ActionName["nomAction"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Historique de Trade</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <link rel="stylesheet" href="../static/style/historique.css">
</head>
<body>
    <nav class="navbar menu-padding-50">
        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" class="icon">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.75 11.25L10.25 5.75"></path>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.75 19.2502H6.25C6.80229 19.2502 7.25 18.8025 7.25 18.2502V15.75C7.25 15.1977 6.80229 14.75 6.25 14.75H5.75C5.19772 14.75 4.75 15.1977 4.75 15.75V18.2502C4.75 18.8025 5.19772 19.2502 5.75 19.2502Z"></path>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.75 19.2502H12.25C12.8023 19.2502 13.25 18.8025 13.25 18.2502V12.75C13.25 12.1977 12.8023 11.75 12.25 11.75H11.75C11.1977 11.75 10.75 12.1977 10.75 12.75V18.2502C10.75 18.8025 11.1977 19.2502 11.75 19.2502Z"></path>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.75 19.2502H18.25C18.8023 19.2502 19.25 18.8025 19.25 18.2502V5.75C19.25 5.19772 18.8023 4.75 18.25 4.75H17.75C17.1977 4.75 16.75 5.19772 16.75 5.75V18.2502C16.75 18.8025 17.1977 19.2502 17.75 19.2502Z"></path>
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 8.25V4.75H7.75"></path>
        </svg>
        <a href="#" class="logo">Virtual Trader</a>
        <div class="nav-links">
            <ul>
                <li><a href="voir_stocks.php?ID_Action=1">Voir les stocks</a></li>
                <li class="active"><a href="historique_trade.php">Historique</a></li>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="classement.php">Classement</a></li>
                <li><a href="amis.php">Amis</a></li>
                <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
        </div>
    </nav>
    <div class="menu_divider"></div>

    <div class="main_content">
        <div class="historique_title"> Historique de trade </div>
        <?php
        $req = $bdd->prepare('SELECT * FROM historiquetrade WHERE ID_User = ? ORDER BY mois DESC');
        $req -> execute(array($dataUser["ID_User"]));
        ?>
        <div class="tableau_historique">
            <table>
                <tr>
                    <div class="titres">
                        <th class="label-titres"><p>Mois</p></th>
                        <th class="label-titres"><p>Action</p></th>
                        <th class="label-titres"><p>Achat / Vente</p></th>
                        <th class="label-titres"><p>Quantité</p></th>
                        <th class="label-titres"><p>Bénéfice / Perte</p></th>
                    </div>
                </tr>
                <div class="tab_corps">
                <?php
                    $donnees = $req->fetch();
                    while($donnees != null){
                        //Permet d'avoir les informations sur chaque lignes selon le statut de transaction
                        $av = null;
                        $action = null;
                        $css = null;
                        $data_gain = null;
                        $quantite = null;
                        switch ($donnees["statut"]){
                            case 0:
                                $action = ActionParser($bdd, $donnees["ID_Action"]);
                                $av = "Achat";
                                $quantite = $donnees["nombreAction"];
                                $data_gain = "-".Benefice($bdd, $donnees["ID_Action"], $donnees["mois"], $donnees["nombreAction"]);
                                $css = "achat_statut";
                                break;
                            case 1:
                                $action = ActionParser($bdd, $donnees["ID_Action"]);
                                $av = "Vente";
                                $quantite = $donnees["nombreAction"];
                                $data_gain = "+". Benefice($bdd, $donnees["ID_Action"], $donnees["mois"], $donnees["nombreAction"]);
                                $css = "vente_statut";
                                break;
                            case 2:
                                $action = "Flux de Dividende";
                                $av = "Dividende";
                                $quantite = "N/A";
                                $data_gain = "+". $donnees["nombreAction"];
                                $css = "dividende_statut";
                                break;
                            case 3:
                                $action = "Argent Emprunté";
                                $av = "Emprunt";
                                $quantite = "N/A";
                                $data_gain = "+".$donnees["nombreAction"];
                                $css = "emprunt_gain";
                                break;
                            case 4:
                                $action = "Remboursement d'Emprunt";
                                $av = "Remboursement";
                                $quantite = "N/A";
                                $data_gain = "-".$donnees["nombreAction"];
                                $css = "emprunt_remb";
                                break;
                        }
                        // Créé chaque lignes de tableaux
                        echo "<tr>
                            <td> ". $donnees["mois"] ." </td>
                            <td> ". $action ."</td>
                            <td class='". $css. "'> ". $av ." </td>
                            <td> ". $quantite ." </td>
                            <td class='". $css ."'> ". $data_gain ." </td>
                        </tr>";
                        $donnees = $req->fetch(); //passe a la prochaine ligne.
                    }
                ?>
                </div>
            </table>
        </div>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>