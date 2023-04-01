<?php
    session_start();
    require_once 'bdd.php';

    include_once('remember.php');
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT email,pseudo,ID_User FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}

    if(isset($_GET['rechercher']) && !empty($_GET['searchAmi'])){
        $search = htmlspecialchars($_GET['searchAmi']);
        $allUsers=$bdd->prepare("SELECT pseudo,token,ID_User FROM user WHERE pseudo LIKE ? EXCEPT (SELECT pseudo,token,ID_User FROM user WHERE email=? or email=?)");
        $allUsers->execute(array('%'.$search.'%',"virtualtrader23@gmail.com",$_SESSION['email']));
    }
    $reqAmiFollower = $bdd->prepare('SELECT * FROM amis INNER JOIN user ON amis.ID_Follower=user.ID_User WHERE user.token = ? AND amis.statut = ?');
    $reqAmiFollower->execute(array($_SESSION['user'],1));
    $nbAmiFollower = $reqAmiFollower->rowCount(); 
    $reqAmiFollowed = $bdd->prepare('SELECT * FROM amis INNER JOIN user ON amis.ID_Followed=user.ID_User WHERE user.token = ? AND amis.statut = ?');
    $reqAmiFollowed->execute(array($_SESSION['user'],1));
    $nbAmiFollowed = $reqAmiFollowed->rowCount(); 
    if(isset($_GET['add'] ) && !empty($_GET['add'])){
        $reqIdAdd = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdAdd->execute(array($_GET['add']));
        $idAdd = $reqIdAdd->fetch();
        $addUser= $bdd->prepare('INSERT INTO amis(ID_Follower,ID_Followed,statut) VALUE(?,?,?)');
        $addUser->execute(array($dataUser['ID_User'],$idAdd['ID_User'],0));
        header('Location: amis.php');
    }
    if(isset($_GET['supprime'] ) && !empty($_GET['supprime'])){
        $reqIdSupr = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdSupr->execute(array($_GET['supprime']));
        $idSupr = $reqIdSupr->fetch();
        $suprAmi = $bdd->prepare('DELETE FROM amis WHERE (ID_Follower=? AND ID_Followed = ?) OR (ID_Follower=? AND ID_Followed = ?)');
        $suprAmi-> execute(array($idSupr['ID_User'],$dataUser['ID_User'],$dataUser['ID_User'],$idSupr['ID_User']));
        header('Location: amis.php');
    }
    $reqDemande = $bdd->prepare('SELECT pseudo,token FROM user INNER JOIN amis ON user.ID_User=amis.ID_Follower WHERE amis.ID_Followed = ? AND amis.statut = ? ');
    $reqDemande->execute(array($dataUser['ID_User'],0));
    $nbDemande = $reqDemande->rowCount();
    if(isset($_GET['accept'] ) && !empty($_GET['accept'])){
        $reqIdAccept = $bdd->prepare('SELECT ID_User FROM user WHERE token=?');
        $reqIdAccept->execute(array($_GET['accept']));
        $idAccept = $reqIdAccept->fetch();
        $acceptAmi = $bdd->prepare('UPDATE amis SET statut=? WHERE ID_Follower=? AND ID_Followed = ?');
        $acceptAmi-> execute(array(1,$idAccept['ID_User'],$dataUser['ID_User']));
        header('Location: amis.php');
    }
    if(isset($_GET['refuse'] ) && !empty($_GET['refuse'])){
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
                    <li><a href="voir_stocks.php">Voir les stocks</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="#">Historique</a></li>
                    <li class="active"><a href="amis.php">Amis</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <div class="search">
            <form method="get">
                <ion-icon name="search-outline"></ion-icon>
                <input type="search" name="searchAmi" placeholder="Rechercher un joueur" class="barreRecherche">
                <input type="submit" name="rechercher" value="rechercher" class="buttonRecherche">
            </form>
        </div>
        <div class="searchJoueur">
            <?php 
                if(isset($allUsers)){
                $verfifUser = $allUsers->rowCount();
                if($verfifUser > 0){
                    foreach($allUsers as $user){
                        ?>
                        <div>
                            <div><a href="amis.php?profil=<?= $user['token']?>"><?= $user['pseudo']?></a> </div>
                        </div>
                        <?php
                        }
                    }else{echo "ce joueur n'existe pas";}
                }   
            ?>
        </div>
        <div class="amis">
            <?php 
                if($nbAmiFollower + $nbAmiFollowed>0){
                    if($nbAmiFollower +$nbAmiFollowed >1){
                        ?>
                        <h2 class="h2-amis">Liste d'ami(e)s</h2>
                        <?php
                    }else{
                        ?>
                        <h2 class="h2-amis">Liste d'ami(e)</h2>
                        <?php
                    }
                    foreach($reqAmiFollower as $amiFollower){
                        $reqAmiFollower = $bdd->prepare('SELECT pseudo,token FROM user WHERE ID_User = ?');
                        $reqAmiFollower->execute(array($amiFollower['ID_Followed']));
                        $pseudoAmiFollower = $reqAmiFollower->fetch();
                    ?>
                        <div><a href="amis.php?profil=<?= $pseudoAmiFollower['token']?>"><?= $pseudoAmiFollower['pseudo']?></a></div>
                    <?php 
                    
                    } 
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
                    <div>Vous n'avez pas encore d'ami</div>
                    <?php  
                }
            ?>
        </div>
        <?php 
            if(isset($_GET['profil'])){
                $reqProfil = $bdd->prepare('SELECT pseudo, soldeJoueur,ID_User FROM user WHERE token = ?');
                $reqProfil->execute(array($_GET['profil']));
                $amiInfo = $reqProfil->fetch();
                $reqVerifAmi = $bdd->prepare('SELECT statut FROM amis WHERE (ID_Follower = ? AND ID_Followed = ?) OR (ID_Follower = ? AND ID_Followed = ?) ');
                $reqVerifAmi->execute(array($dataUser['ID_User'],$amiInfo['ID_User'],$amiInfo['ID_User'],$dataUser['ID_User']));
                $verifAmi = $reqVerifAmi->fetch();
                ?>
                <div class="info-ami">  
                <div>
                    <label for="pseudo"><ion-icon name="person-outline"></ion-icon> Pseudo :</label>
                    <input type="text" name="pseudo" value="<?php echo $amiInfo['pseudo'] ?>" readonly>
                </div>
                <div>
                    <label for="nom"><ion-icon name="cash-outline"></ion-icon> Porte monnaie actuel :</label>
                    <input type="text" name="soldeUser" value="<?php echo $amiInfo['soldeJoueur'] ?>" readonly>
                </div>
                <?php 
                if($verifAmi){
                    ?>
                     <div>
                    <form method="get">
                        <button class="button-supr"><a href="amis.php?supprime=<?= $_GET['profil'] ?>">Supprimer</a></button>
                    </form>
                    </div>
                <?php }
                else{
                    ?>
                    <form method="get">
                    <button class="button-supr"><a href="amis.php?add=<?= $_GET['profil'] ?>">Ajouter</a></button>
                    </form>
                    <?php }
                    ?>                
                </div>
            <?php }
        ?>
        <div class="demande">
        <?php 
            if($nbDemande>0){
                    ?>
                    <h2>Demandes d'amis : <?= $nbDemande?></h2>
                    <?php
                foreach($reqDemande as $demande){;
                ?>
                    <div><a href="amis.php?profil=<?= $demande['token']?>"><?= $demande['pseudo']?></a></div>
                    <div>
                        <form method="get">
                            <button><a href="amis.php?accept=<?= $demande['token'] ?>">Accepter</a></button>
                        </form>
                    </div>
                    <div>
                        <form method="get">
                            <button><a href="amis.php?refuse=<?= $demande['token'] ?>">Refuser</a></button>
                        </form>
                    </div>
                <?php 
                } 
            }else{
                ?>
                <div>Vous n'avez pas de demande d'ami</div>
                <?php  
            }
        ?>
        </div>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    </body>
</html>