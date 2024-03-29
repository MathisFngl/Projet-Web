<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    require_once 'calculTotalArgent.php';
require 'nouveauTour.php';

//  on recupère les informations d'un joueur via son token
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT email,pseudo,ID_User,photo FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    /* rechercher un ami contenant les caractères envoyé sauf admin et propre joueur connecté*/
    if(isset($_GET['rechercher']) && !empty($_GET['searchAmi'])){
        $search = htmlspecialchars($_GET['searchAmi']);
        $allUsers=$bdd->prepare("SELECT pseudo,token,ID_User FROM user WHERE pseudo LIKE ? EXCEPT (SELECT pseudo,token,ID_User FROM user WHERE statut=? or ID_User=?)");
        $allUsers->execute(array('%'.$search.'%',1,$dataUser['ID_User']));
    }

    /*liste d'amis si on est le suiveur de base*/
    $reqAmiFollower = $bdd->prepare('SELECT * FROM amis INNER JOIN user ON amis.ID_Follower=user.ID_User WHERE user.token = ? AND amis.statut = ?');
    $reqAmiFollower->execute(array($_SESSION['user'],1));
    $nbAmiFollower = $reqAmiFollower->rowCount();

    /*liste d'amis si on est suivi de base*/
    $reqAmiFollowed = $bdd->prepare('SELECT * FROM amis INNER JOIN user ON amis.ID_Followed=user.ID_User WHERE user.token = ? AND amis.statut = ?');
    $reqAmiFollowed->execute(array($_SESSION['user'],1));
    $nbAmiFollowed = $reqAmiFollowed->rowCount(); 

    /* ajout d'ami en insérant une nouvelle ligne dans la table ami*/
    if(isset($_GET['add'] ) && !empty($_GET['add'])){
        $reqIdAdd = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdAdd->execute(array($_GET['add']));
        $idAdd = $reqIdAdd->fetch();
        $addUser= $bdd->prepare('INSERT INTO amis(ID_Follower,ID_Followed,statut) VALUE(?,?,?)');
        $addUser->execute(array($dataUser['ID_User'],$idAdd['ID_User'],0));
        header('Location: amis.php');
    }

    /* suppression d'ami en supprimant la ligne dans la table ami*/
    if(isset($_GET['supprime'] ) && !empty($_GET['supprime'])){
        $reqIdSupr = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdSupr->execute(array($_GET['supprime']));
        $idSupr = $reqIdSupr->fetch();
        $suprAmi = $bdd->prepare('DELETE FROM amis WHERE (ID_Follower=? AND ID_Followed = ?) OR (ID_Follower=? AND ID_Followed = ?)');
        $suprAmi-> execute(array($idSupr['ID_User'],$dataUser['ID_User'],$dataUser['ID_User'],$idSupr['ID_User']));
        header('Location: amis.php');
    }

    /* demande d'ami c'est a dire si le joueur suivi est celui connecté */
    $reqDemande = $bdd->prepare('SELECT pseudo,token FROM user INNER JOIN amis ON user.ID_User=amis.ID_Follower WHERE amis.ID_Followed = ? AND amis.statut = ? ');
    $reqDemande->execute(array($dataUser['ID_User'],0));
    $nbDemande = $reqDemande->rowCount();

    /* accepter : on passe le statut à 1 */
    if(isset($_GET['accept'] ) && !empty($_GET['accept'])){
         /* récupération de l'id du suiveur */
        $reqIdAccept = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdAccept->execute(array($_GET['accept']));
        $idAccept = $reqIdAccept->fetch();
        $acceptAmi = $bdd->prepare('UPDATE amis SET statut=? WHERE ID_Follower=? AND ID_Followed = ?');
        $acceptAmi-> execute(array(1,$idAccept['ID_User'],$dataUser['ID_User']));
        header('Location: amis.php');
    }
    /*refuser : on supprime la ligne*/
    if(isset($_GET['refuse'] ) && !empty($_GET['refuse'])){
        /* récupération de l'id du suiveur */
        $reqIdRefuse = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdRefuse->execute(array($_GET['refuse']));
        $idRefuse = $reqIdRefuse->fetch();
        $refuseAmi = $bdd->prepare('DELETE FROM amis WHERE ID_Follower=? AND ID_Followed = ?');
        $refuseAmi-> execute(array($idRefuse['ID_User'],$dataUser['ID_User'],));
        header('Location: amis.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Amis</title>
    <link rel="stylesheet" href="../static/style/style.css">
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
                    <li><a href="classement.php">Classement</a></li>
                    <li class="active"><a href="amis.php">Amis</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <div class="search">   
            <form method="get" class="search-bar">
                <input type="search" name="searchAmi" pattern=".*\S.*" required>
                <button type="submit" name="rechercher" class="search-btn"></button>
            </form>
        </div>
        <!-- rechercher une joueur -->
        <div class="searchJoueur">
            <?php 
                if(isset($allUsers)){
                $verfifUser = $allUsers->rowCount();
                if($verfifUser > 0){
                    ?>
                    <?php
                    foreach($allUsers as $user){
                        ?>
                        <div class="joueurSearch">
                            <div><a href="amis.php?profil=<?= $user['token']?>"><?= $user['pseudo']?></a></div>
                        </div>
                        <?php
                        }
                    }else{echo "Ce joueur n'existe pas";}
                }   
            ?>
        </div>
        <!-- lister les amis -->
        <div class="amis">
            <?php
                if($nbAmiFollower + $nbAmiFollowed>0){
                    if($nbAmiFollower +$nbAmiFollowed >1){
                        ?>
                        <h2 class="h2-ami">Liste d'amis</h2>
                        <?php
                    }else{ 
                        ?>
                        <h2 class="h2-ami">Liste d'ami</h2>
                        <?php
                    }
                    // amis qui nous suivent
                    foreach($reqAmiFollower as $amiFollower){
                        $reqAmiFollower = $bdd->prepare('SELECT pseudo,token FROM user WHERE ID_User = ?');
                        $reqAmiFollower->execute(array($amiFollower['ID_Followed']));
                        $pseudoAmiFollower = $reqAmiFollower->fetch();
                    ?>
                        <div><a href="amis.php?profil=<?= $pseudoAmiFollower['token']?>"><?= $pseudoAmiFollower['pseudo']?></a></div>
                    <?php 
                    
                    } 
                    // amis que l'on suit
                    foreach($reqAmiFollowed as $amiFollowed){
                        $reqAmiFollowed = $bdd->prepare('SELECT pseudo,token FROM user WHERE ID_User = ?');
                        $reqAmiFollowed->execute(array($amiFollowed['ID_Follower']));
                        $pseudoAmiFollowed = $reqAmiFollowed->fetch();
                    ?>
                        <div><a href="amis.php?profil=<?= $pseudoAmiFollowed['token']?>"><?= $pseudoAmiFollowed['pseudo']?></a></div>
                    <?php 
                    
                    } 
                }else{
                    ?>
                    <h2 class="h2-ami">Liste d'ami</h2><br />
                    <div><p>Vous n'avez pas encore d'ami (voulez-vous un Curly ?)</p></div>
                    <?php  
                }
            ?>
        </div>
        <!-- affichage des quelques informations de la personne  -->
        <?php 
            if(isset($_GET['profil'])){
                $reqProfil = $bdd->prepare('SELECT pseudo, soldeJoueur,ID_User,photo FROM user WHERE token = ?');
                $reqProfil->execute(array($_GET['profil']));
                $amiInfo = $reqProfil->fetch();
                $photo = $bdd->prepare('SELECT photo FROM photo WHERE ID_Photo = ?');
                $photo->execute(array($amiInfo['photo']));
                $image = $photo->fetch();
                $reqVerifAmi = $bdd->prepare('SELECT statut FROM amis WHERE (ID_Follower = ? AND ID_Followed = ?) OR (ID_Follower = ? AND ID_Followed = ?) ');
                $reqVerifAmi->execute(array($dataUser['ID_User'],$amiInfo['ID_User'],$amiInfo['ID_User'],$dataUser['ID_User']));
                $verifAmi = $reqVerifAmi->fetch();
                ?>
                <div class="info-ami">  
                <button class="close-profil"><a href="amis.php">X</a></button>  
                    <div class="amiPhoto">
                        <?= '<img src="data:image/jpeg;base64,'.base64_encode($image['photo']).'" alt="photo de profil">' ?>
                    </div>
                <div>
                    <label for="pseudo"><ion-icon name="person-outline"></ion-icon> Pseudo :</label>
                    <input type="text" name="pseudo" value="<?php echo $amiInfo['pseudo'] ?>" readonly>
                </div>
                <div>
                    <label for="nom"><ion-icon name="cash-outline"></ion-icon> Porte monnaie actuel :</label>
                    <input type="text" name="soldeUser" value="<?php echo ArgentTotal($bdd, $amiInfo["ID_User"]) ?>" readonly>
                </div>
                <?php 
                if($verifAmi){
                    $reqAdd = $bdd->prepare('SELECT ID_Followed FROM amis WHERE statut = ? AND ID_Follower = ?');
                    $reqAdd->execute(array(0,$amiInfo['ID_User']));
                    $verifAdd = $reqAdd->fetch();
                    // si demande alors les boutons accepter / refuser : statut = 0
                    if($verifAdd){
                        ?>
                        <div class="button-profil-ar">
                            <div>
                                <form method="get">
                                    <a href="amis.php?accept=<?= $_GET['profil'] ?>" class="accept-button-profil"><ion-icon name="checkmark-circle-outline"></ion-icon></a>
                                </form>
                            </div>
                            <div>
                                <form method="get">
                                    <a href="amis.php?refuse=<?= $_GET['profil'] ?>" class="refuse-button-profil"><ion-icon name="close-circle-outline"></ion-icon></a>
                                </form>
                            </div> 
                        </div>
                    <?php
                    // si ami alors bouton supprimé : statut = 1
                    }else{
                        ?>
                        <div class="supr-button">
                        <form method="get">
                            <button class="button-supr"><a href="amis.php?supprime=<?= $_GET['profil'] ?>">Supprimer</a></button>
                        </form>
                        </div>
                    <?php }   
                 }
                 //si une recherche alors bouton ajouter : pas de lignes dans la table amis
                else{
                    ?>
                    <div class="supr-button">
                    <form method="get">
                    <button class="button-supr"><a href="amis.php?add=<?= $_GET['profil'] ?>">Ajouter</a></button>
                    </form>
                    </div>
                    <?php }
                    ?>              
                </div>
            <?php }
        ?>
        <!-- liste demande d'ami -->
        <div class="demande">
            <h2 class="h2-demande">Demandes d'amis</h2>
        <?php 
            if($nbDemande>0){
                foreach($reqDemande as $demande){;
                ?>
                    <div>
                        <form method="get">
                            <a href="amis.php?accept=<?= $demande['token'] ?>" class="accept-button"><ion-icon name="checkmark-circle-outline"></ion-icon></a>
                        </form>
                    </div>
                    <div>
                        <form method="get">
                            <a href="amis.php?refuse=<?= $demande['token'] ?>" class="refuse-button"><ion-icon name="close-circle-outline"></ion-icon></a>
                        </form>
                    </div>
                    <div class="demande-pseudo"><a href="amis.php?profil=<?= $demande['token']?>"><?= $demande['pseudo']?></a></div>
                <?php 
                } 
            }else{
                ?>
                <div><p>Vous n'avez pas de demande d'ami</p></div>
                <?php  
            }
        ?>
        </div>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>