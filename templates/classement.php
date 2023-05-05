<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    require_once 'calculTotalArgent.php';
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT email,pseudo,soldeJoueur,photo FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    $photo = $bdd->prepare('SELECT photo FROM photo WHERE ID_Photo = ?');
    $photo->execute(array($dataUser['photo']));
    $image = $photo->fetch();
    /* selectionner les joueurs */
    $reqJoueur = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user EXCEPT (SELECT pseudo,soldeJoueur, ID_User FROM user WHERE email="virtualtrader23@gmail.com")');
    $reqJoueur->execute();

    $nbJoueur = $bdd->prepare('SELECT COUNT(*) FROM user');
    $nbJoueur->execute();
    $nb = $nbJoueur->fetch();

    $classement = $reqJoueur->fetch();
    $classement_j = [];
    for($j = 0; $j < $nb[0]-1; $j++){
        $temp = [$classement["pseudo"], ArgentTotal($bdd,$classement["ID_User"])];
        array_push($classement_j, $temp);
        $classement = $reqJoueur->fetch();
    }

    function sort_by_second_element($a, $b) {
        return $b[1] - $a[1];
    }

    // Sort the array using the defined function
    usort($classement_j, 'sort_by_second_element');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
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
                    <li><a href="historique_trade.php">Historique</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li class="active"><a href="classement.php">Classement</a></li>
                    <li><a href="amis.php">Amis</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>

        <div class = "main_content">
            <div class="historique_title">Classement des joueurs</div>
            <div class="tableau_historique">
                <table>
                    <tr>
                        <div class="titres">
                            <th class="label-titres"><p>Classement</p></th>
                            <th class="label-titres"><p>Nom</p></th>
                            <th class="label-titres"><p>Solde Total</p></th>
                        </div>
                    </tr>
                    <div class="tab_corps">
                        <?php
                        $i = 1;
                        while($i < $nb[0]){
                            echo "<tr>
                                <td class='" . (($i == 1) ? 'f' : (($i == 2) ? 's' : (($i == 3) ? 't' : 'no_podium'))). "'> ". $i ." </td>
                                <td class='" . (($i == 1) ? 'f' : (($i == 2) ? 's' : (($i == 3) ? 't' : 'no_podium'))). "'> ".$classement_j[$i-1][0]."</td>
                                <td class='" . (($i == 1) ? 'f' : (($i == 2) ? 's' : (($i == 3) ? 't' : 'no_podium'))). "'>". $classement_j[$i-1][1]." $"." </td>
                            </tr>";
                            $i++;
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