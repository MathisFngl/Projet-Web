<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');
    require_once 'calculTotalArgent.php';

     //  on recupère les informations d'un joueur via son token + on vérifie s'il est connecté
    if(isset($_SESSION['user'])){
        $requUser = $bdd->prepare('SELECT ID_User, email,pseudo,soldeJoueur,photo,nbPartie FROM user WHERE token = ?');
        $requUser->execute(array($_SESSION['user']));
        $dataUser = $requUser->fetch();
    }else{header('Location: deconnexion.php');}
   
    // récupération de l'avatar
    $photo = $bdd->prepare('SELECT photo FROM photo WHERE ID_Photo = ?');
    $photo->execute(array($dataUser['photo']));
    $image = $photo->fetch();
    
    // récupération des emprunts
    $reqEmprunt = $bdd->prepare('SELECT moisEmprunt, soldeEmprunt FROM emprunt WHERE ID_User = ?');
    $reqEmprunt->execute(array($dataUser['ID_User']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li class="active"><a href="profile.php">Profil</a></li>
                    <li><a href="classement.php">Classement</a></li>
                    <li><a href="amis.php">Amis</a></li>
                    <li><a href="deconnexion.php">Déconnexion</a></li>
                </ul>
            </div>
        </nav>
        <div class="menu_divider"></div>
        <div class="profil_body">
            <!-- affichage des informations du profil -->
            <div class="infoProfil">
                <div class="left_panel">
                    <div class="profilPhoto">
                        <?= '<img src="data:image/jpeg;base64,'.base64_encode($image['photo']).'" alt="photo de profil">' ?>
                    </div>
                    <button class="modifProfil"><a href="modifProfil.php">Modifier mon profil</a></button>
                    <div class="flex-container">
                        <button class="show-modal">Faire un emprunt</button>
                    </div>
                    <div class="modal hidden">
                        <button class="close-modal"><a href="profile.php">&times;</a></button>
                        <h2 class="emprunt1-h2">Emprunt</h2>
                        <!-- affichage des emprunts -->
                        <div class="emprunt">
                            <table>
                                <tr>
                                    <div class="titres">
                                        <th class="label-titres"><p>Solde</p></th>
                                        <th class="label-titres"><p>Nombre mois</p></th>
                                    </div>
                                </tr>
                                <div class="tab_corps">
                                    <?php 
                                        foreach($reqEmprunt as $emprunt){
                                    ?> <tr>
                                        <td><?= $emprunt['soldeEmprunt'] ?></td>
                                        <td><?= $emprunt['moisEmprunt']?></td>
                                    </tr>   
                                    <?php }
                                    ?>
                                </div>
                            </table>
                        </div>
                        <!-- faire un emprunt -->
                        <form action="emprunt.php" method="post">
                            <h2 class="emprunt2-h2">Faire un emprunt</h2>
                            <div class="demEmprunt">
                                <div class="soldeEmprunt">
                                    <label for="soldeEmprunt">Quantité d'argent à emprunter :</label>
                                    <input class="" type="number" name="soldeEmprunt" value="0" min="0" max="100000000"  id="soldeEmprunt"></input>
                                </div>
                                <div class="moisEmprunt">
                                    <label for="moisEmprunt">Nombre de mois pour rembourser :</label>
                                    <input class="" type="number" name="moisEmprunt" value="1" min="1" max="24"  id="moisEmprunt"></input>
                                </div>
                                <button type="submit" name="emprunt" class="buton-emprunt">Emprunter</button>
                            </div>
                        </form>
                        <?php 
                            if(isset($_GET['emprunt'])){
                                if($_GET['emprunt'] == 1){
                                    echo "<p class= 'empruntFalse'>Vous n'avez pas assez d'argent pour faire cet emprunt";
                                }
                            }
                        ?>
                    </div>
                    <div class="overlay hidden"></div>
                    <script src="../js/modal.js"></script>
                    <script>
                        // Vérifie si l'URL contient "modal=1" et ouvre la fenêtre modale correspondante
                        const urlParams = new URLSearchParams(window.location.search);
                        const modalSearch = urlParams.get("emprunt");
                        if (modalSearch == 1) {
                            openModal();
                        }
                    </script>
                    <button class="modifProfil"><a href="gameOver.php"> Abandonner la partie</a></button>
                </div>
                <div class="right_panel">
                    <div class="info-user">
                        <div>
                            <label for="pseudo"><ion-icon name="person-outline"></ion-icon> Pseudo :</label>
                            <input type="text" name="pseudo" value="<?php echo $dataUser['pseudo'] ?>" readonly>
                        </div>
                        <div>
                            <label for="nom"><ion-icon name="mail-outline"></ion-icon> Email :</label>
                            <input type="email" name="email" value="<?php echo $dataUser['email'] ?>" readonly>
                        </div>
                        <div>
                            <label for="nom"><ion-icon name="checkmark-outline"></ion-icon> Nombre parties jouées :</label>
                            <input type="text" name="nbParties" value="<?php echo $dataUser['nbPartie'] ?>" readonly>
                        </div>
                        <div>
                            <label for="nom"><ion-icon name="cash-outline"></ion-icon> Porte monnaie actuel :</label>
                            <input type="text" name="soldeUser" value="<?php echo ArgentTotal($bdd, $dataUser['ID_User']) ?>$" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
            <div class="portefeuille_historique">
                <?php
                    // Créé le tableau de l'historique qui sera affiché dans le profil (maximum 12 mois)
                    $data_array = [];
                    $CountLineReq = $bdd->prepare("SELECT COUNT(solde) FROM historiqueportefeuille WHERE ID_User = ?");
                    $CountLineReq -> execute(array($dataUser['ID_User']));
                    $nbLine = $CountLineReq -> fetch();
                    $nbLine = $nbLine[0];

                    if($nbLine>12){
                        $nbLine = 12;
                    }

                    $reqHistoriqueSolde = $bdd->prepare("SELECT solde FROM historiqueportefeuille WHERE ID_User = ? ORDER BY mois DESC LIMIT 12");
                    $reqHistoriqueSolde -> execute(array($dataUser['ID_User']));

                for($i = 0; $i < $nbLine; $i++){
                    $portefeuille_temp = $reqHistoriqueSolde -> fetch();
                    $data_temp = $portefeuille_temp["solde"];
                    array_push($data_array, $data_temp);
                }
                ?>
                <canvas id="myChart" class="chart"></canvas>
                <script>var ctx = document.getElementById('myChart').getContext('2d');

                    const data_array = <?php echo json_encode($data_array, JSON_NUMERIC_CHECK); ?>; // Récupère le tableau en Javascript
                    data_array.reverse();

                    const n = <?php echo $nbLine ?>;
                    const label = Array.from({length: n}, (_, i) => i + 1) // Créé un tableau de la forme [0,1,2,3,...] de taille n
                    var data = {
                        labels: label,
                        datasets: [{
                            label: 'Solde mensuel',
                            data: data_array,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            fill: true
                        }]
                    };

                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: data,
                        options: {}
                    });
                </script>
            </div>
        </div>
    </body>
</html>