<?php
    session_start();
    require_once 'bdd.php';

    require('remember.php');
//  on recupère les informations d'un joueur via son token + on vérifie s'il est connecté et que un admin
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT statut FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }
    if(isset($dataUser['statut']) AND $dataUser['statut'] == 1){

    }else{ //si pas admin (statut =1) alors deconnexion
        header('Location: deconnexion.php');
    }   

    /*suppression d'un personne en supprimant sa ligne dans la table user */
    if(isset($_GET['supprime']) AND !empty($_GET['supprime'])){
        $idSupr = (int) $_GET['supprime'];

        $suprUser = $bdd->prepare('DELETE FROM user WHERE ID_USER = ?');
        $suprUser-> execute(array($idSupr));
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <link rel="stylesheet" href="../static/style/admin.css">
</head>
    <body>
        <nav class="navbar menu-padding-20">
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
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <div class="menu_divider"></div>
        <div class="titre_admin">Panneau de contrôle administrateur</div>
        <div class="infoUser">
            <table>
                <tr>
                    <div class="titres">
                        <th class="label-titres"><p>ID</p></th>
                        <th class="label-titres"><p>Pseudo</p></th>
                        <th class="label-titres"><p>Email</p></th>
                        <th class="label-titres"><p>Actions</p></th>
                    </div>
                </tr>
                <?php
                // récupère tout  les informations de tout les joueurs et les affichent joueur par joueur dans un tableau
                foreach($bdd->query('SELECT * FROM user EXCEPT (SELECT * FROM user WHERE email="virtualtrader23@gmail.com")') as $infoUser ){ 
                ?>
                <div class="tabAdmin">
                    <tr>
                        <td class="items"><?= $infoUser["ID_User"] ?></td>
                        <td class="items"><?= $infoUser["pseudo"] ?> </td>
                        <td class="items"><?= $infoUser["email"] ?> </td>
                        <td class="actions"><form method="get">
                            <button><a href="admin.php?supprime=<?= $infoUser["ID_User"] ?>">Supprimer</a></button>
                        </form></td>
                    </tr>
                </div>
                <?php
                }
                ?>
            </table>
        </div>
    </body>
</html>