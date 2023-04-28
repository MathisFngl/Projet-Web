<?php
  session_start();
  require_once 'bdd.php';
  require('remember.php');
  if(isset($_SESSION['user'])){
    $requUser = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user WHERE token = ?');
    $token = $_SESSION['user'];
    $requUser->execute(array($token));
    $dataUser = $requUser->fetch();
  }else{header('Location: deconnexion.php');}
  $reqMaxPrice = $bdd->prepare('SELECT MAX(prix) as prix FROM historiqueaction ORDER BY mois DESC');
  $reqMaxPrice->execute();
  $maxPrice = $reqMaxPrice->fetch();
  $nbSearchAction = 0;
  $nbPrixAction = 0;
    if(!empty($_POST['searchAction']) || !empty($_POST['periode']) || !empty($_POST['pourcentage']) || !empty($_POST['triPrix'])){
        if(!empty($_POST['searchAction'])){
            $reqSearchAction = $bdd->prepare('SELECT nomAction,ID_Action FROM dataaction WHERE nomAction LIKE ?');
            $reqSearchAction->execute(array('%'.$_POST['searchAction'].'%'));
            $searchAction = $reqSearchAction->fetchAll();
            $nbSearchAction = $reqSearchAction->rowCount();
        }
        if(!empty($_POST['periode']) && !empty($_POST['pourcentage'])){
            if($_POST['periode'] == "mois"){
              $reqID_Action = $bdd->prepare('SELECT ID_Action FROM dataaction');
              $reqID_Action->execute();
              $allID_Action = $reqID_Action->fetchall();
              $countID_Action = $reqID_Action->rowCount();
              $nbPercentAction = 0;
              $tabPercentAction = [];
              for($i=0; $i<$countID_Action;$i++){
                $reqMoisPercent1= $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois=(SELECT MAX(mois)-1 FROM historiqueaction)");
                $reqMoisPercent1 -> execute(array($allID_Action[$i]['ID_Action']));
                $price1_raw = $reqMoisPercent1->fetch();
                $reqMoisPercent2= $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois=(SELECT MAX(mois)-2 FROM historiqueaction)");
                $reqMoisPercent2 -> execute(array($allID_Action[$i]['ID_Action']));
                $price2_raw = $reqMoisPercent2->fetch();
                $price1 = $price1_raw["prix"];
                $price2 = $price2_raw["prix"];
                $percent_action = (($price1 - $price2) / $price2) * 100.0;
                $percent_action_rounded = number_format($percent_action, 1);
                if($percent_action_rounded == $_POST['pourcentage']){
                  $reqPercentAction = $bdd->prepare('SELECT ID_Action,nomAction FROM dataaction WHERE ID_Action = ?');
                  $reqPercentAction->execute(array($allID_Action[$i]['ID_Action']));
                  $actionPercent = $reqPercentAction->fetchAll();
                  $nbPercentAction = $nbPercentAction +$reqPercentAction->rowCount();
                  array_push($tabPercentAction,$actionPercent);
                }
                
              }
              
            }
            else if($_POST['periode'] == "annee"){
              $reqID_Action = $bdd->prepare('SELECT ID_Action FROM dataaction');
              $reqID_Action->execute();
              $allID_Action = $reqID_Action->fetchall();
              $countID_Action = $reqID_Action->rowCount();
              $nbPercentAction = 0;
              $tabPercentAction = [];
              for($i=0; $i<$countID_Action;$i++){
                $reqMoisPercent1= $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois=(SELECT MAX(mois)-1 FROM historiqueaction)");
                $reqMoisPercent1 -> execute(array($allID_Action[$i]['ID_Action']));
                $price1_raw = $reqMoisPercent1->fetch();
                $reqMoisPercent2= $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois=(SELECT MAX(mois)-13 FROM historiqueaction)");
                $reqMoisPercent2 -> execute(array($allID_Action[$i]['ID_Action']));
                $price2_raw = $reqMoisPercent2->fetch();
                $price1 = $price1_raw["prix"];
                $price2 = $price2_raw["prix"];
                $percent_action = (($price1 - $price2) / $price2) * 100.0;
                $percent_action_rounded = number_format($percent_action, 1);
                if($percent_action_rounded == $_POST['pourcentage']){
                  $reqPercentAction = $bdd->prepare('SELECT ID_Action,nomAction FROM dataaction WHERE ID_Action = ?');
                  $reqPercentAction->execute(array($allID_Action[$i]['ID_Action']));
                  $actionPercent = $reqPercentAction->fetchAll();
                  $nbPercentAction = $nbPercentAction +$reqPercentAction->rowCount();
                  array_push($tabPercentAction,$actionPercent);
                }
                
              } 
            }
        }
        if(!empty($_POST['triPrix']) && $_POST['triPrix'] != 0){
            $reqPrixAction = $bdd->prepare('SELECT dataaction.nomAction,dataaction.ID_Action FROM dataaction INNER JOIN historiqueaction ON dataaction.ID_Action = historiqueaction.ID_Action WHERE historiqueaction.prix = ? AND mois=(SELECT MAX(mois)-1 FROM historiqueaction)');
            $reqPrixAction->execute(array($_POST['triPrix']));
            $prixAction = $reqPrixAction->fetchall();  
            $nbPrixAction = $reqPrixAction->rowCount();          
        }  
    }
?>

<!DOCTYPE html>

<html>
  <head>
    <metacharset="utf-8">
    <title>Stocks</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <link rel="stylesheet" href="../static/style/stock.css">
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
          <li class="active"><a href="#">Stocks</a></li>
          <li><a href="historique_trade.php">Historique</a></li>
          <li><a href="profile.php">Profil</a></li>
          <li><a href="classement.php">Classement</a></li>
          <li><a href="amis.php">Amis</a></li>
          <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
      </div>
    </nav>
    <div class="menu_divider"></div>
    <nav class="sidenav">
      
      <div class="flex-container">
          <button class="show-modal">Trier</button>
      </div>
        <div class="modal hidden">
        <form action="voir_stocks.php?ID_Action=<?=$_GET['ID_Action']?>&modal=1" method="post">
          <button class="close-modal"><a href="voir_stocks.php?ID_Action=<?=$_GET['ID_Action']?>">&times;</a></button>
          <h2 class="trie-h2">Trier les actions</h2>
          <div class=modal-modif>
            <div class="triPrix">
              <label for="triPrix">Prix :</label>
              <input class="range" type="number" name="triPrix" value="0" min="0" max="<?= $maxPrice['prix'] ?>" id="triPrix" step="0.01"></input>
            </div>
            <div class="searchAction">
            <label for="searchAction">Rechercher une Action :</label>
            <input type="search" name="searchAction" placeholder="Action" id="searchAction">
            </div>
            <div class = pourcentage>
            <label for="periode">Période :</label>
            <select name="periode" id="periode">
              <option value="mois">Mois</option>
              <option value="annee">Année</option>
            </select>
  
            <label for="pourcentage">Pourcentage :</label>
            <input type="number" name="pourcentage" id="pourcentage" value="0" step="0.1">
            </div>
            <button type="submit" name="rechercher" class="searchButon">Rechercher</button>
          </div>
          </form>
          <div class="resultAction">
          <h2 class = "h2-resultAction">Résultat :</h2>
          <?php 
          
            if($nbPrixAction>0){
              for($i=0;$i<$nbPrixAction;$i++){
                ?>
                  <div class="actionResult"><a href="voir_stocks.php?ID_Action=<?= $prixAction[$i]['ID_Action']?>"><?= $prixAction[$i]['nomAction']?></a></div>
              <?php }
            }
            if($nbSearchAction>0){
              for($i=0;$i<$nbSearchAction;$i++){
                ?>
                  <div class="actionResult"><a href="voir_stocks.php?ID_Action=<?= $searchAction[$i]['ID_Action']?>"><?= $searchAction[$i]['nomAction']?></a></div>
              <?php }
            }
            if(!isset($nbPercentAction)){
              $nbPercentAction = 0;
            }
            if($nbPercentAction>0){
              for($i=0;$i<$nbPercentAction;$i++){
              
                ?>
                  <div class="actionResult"><a href="voir_stocks.php?ID_Action=<?= $tabPercentAction[$i][0]['ID_Action']?>"><?= $tabPercentAction[$i][0]['nomAction']?></a></div>
              <?php }
            }
            if($nbPrixAction+$nbSearchAction+$nbPercentAction == 0){
              ?>
              <div class="actionResult"><p>Aucun éléments ne correspond à votre recherche</p></div>
            <?php }
          ?>
      </div>
        </div>
        <div class="overlay hidden"></div>
        <script src="../js/modal.js"></script>
        <ul>
            <li class="dropdown"><a class="main_drop" href="#indices">Indices</a>
                <ul>
                    <li> <a href="voir_stocks.php?ID_Action=1">CAC 40</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=2">NASDAQ</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=3">S&P 500</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=4">EURO STOXX 50</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#forex">Forex</a>
                <ul>
                    <li> <a href="voir_stocks.php?ID_Action=5">EUR/USD</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=6">USD/JPY</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=7">USD/CAD</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=8">EUR/GBP</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#crypto">Crypto</a>
                <ul>
                    <li> <a href="voir_stocks.php?ID_Action=9">Bitcoin</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=10">Ethernum</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=11">Tether</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=12">BNB</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#action">Actions</a>
                <ul>
                    <li> <a href="voir_stocks.php?ID_Action=13">AAPL</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=14">META</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=15">AMZN</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=16">NVDA</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#taux-obligation">Taux & Obligations</a>
                <ul>
                    <li> <a href="voir_stocks.php?ID_Action=17">French Bond</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=18">Italian Bond</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=19">German Bond</a> </li>
                    <li> <a href="voir_stocks.php?ID_Action=20">United States Bond</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
        </ul>     
    </nav>
    <script>
        // Vérifie si l'URL contient "modal=1" et ouvre la fenêtre modale correspondante
        const urlParams = new URLSearchParams(window.location.search);
        const modalSearch = urlParams.get("modal");
        if (modalSearch == 1) {
          openModal();
        }
      </script>
      <div class="page-content">
      <div class="trading-panel">
        <div class="graphes-gauche">
          <div class="graphique_main">
            <?php
                include 'update_graph.php';

                if(isset($_GET['ID_Action'] ) && !empty($_GET['ID_Action'])){
                    $ID_Action = $_GET['ID_Action'];
                    $requHistoriquePrix = $bdd->prepare("SELECT nomAction FROM dataaction WHERE ID_Action = ?");
                    $requHistoriquePrix -> execute(array($ID_Action));
                    $dataAction = $requHistoriquePrix->fetch();
                    $nameAction = $dataAction['nomAction'];
                }
                $data_amount = 5;
                $data_array = constructionTableau($bdd, $data_amount, $ID_Action);

                $requHistoriquePrix = $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? ORDER BY mois DESC LIMIT 3");
                $requHistoriquePrix -> execute(array($ID_Action));
                $blob = $requHistoriquePrix->fetch();
                $price1_raw = $requHistoriquePrix->fetch();
                $price2_raw = $requHistoriquePrix->fetch();
                $price1 = $price1_raw["prix"];
                $price2 = $price2_raw["prix"];

                $percent_change = (($price1 - $price2) / $price2) * 100.0;
                $percent_change_rounded = number_format($percent_change, 1);
            ?>

            <div class="bandeau-infos-trade"> <?php echo"$nameAction"?> : Changement du dernier mois : <?php echo "$percent_change_rounded" ?> %</div>
            <div id="MainTrade" class="dim-main-trade"></div>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script>

              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart(){
                  const data_array = <?php echo json_encode($data_array, JSON_NUMERIC_CHECK); ?>;
                  const data_amount = <?php echo json_encode($data_amount); ?>;
                  const data = google.visualization.arrayToDataTable(data_array, true);

                  var options = {
                      'hAxis': {
                          'direction': -1,
                          'gridlines': {
                              'color': 'transparent' // hide horizontal gridlines
                          }
                      },
                    legend:'none',

                    candlestick: {
                          fallingColor: { wickStroke: 'white', strokeWidth: 0, fill: '#a52714' },
                          risingColor: { wickStroke: 'white', strokeWidth: 0, fill: '#0f9d58' }},
                    backgroundColor : { strokeWidth: 0, fill: '#212b36' },
                    colors: ['#f1f1f1'],
                    chartArea: {'width': '90%', 'height': '90%'},
                  };

                  var chart = new google.visualization.CandlestickChart(document.getElementById('MainTrade'));
                  chart.draw(data, options);

                  window.addEventListener('resize', function() {
                    setTimeout(function() {
                    chart.draw(data, options);
                    }, 0);
                  });
                }
            </script>
          </div>
        </div>
        <div class="graphe-droite">
          <form class="achat" method="post" action="achat_vente.php">
            <div class="achat-vente-button">
              <button type="submit" name='buy' class="achat-button"> Acheter </button>
              <button type="submit" name='sell' class='sell-button'> Vendre </button>
            </div>
            <div class="menu_divider" style="margin-bottom: 10px;"></div>
            <div>
            <div class="side-labels">Prix à l'unité :</div>
            <?php
                $prix_courrant_req = $bdd->prepare('SELECT prix FROM historiqueaction WHERE ID_Action = ? AND mois = (SELECT MAX(mois)-1 FROM historiqueaction)');
                $prix_courrant_req->execute(array($ID_Action));
                $prix_courrant = $prix_courrant_req->fetch();
                $prix = $prix_courrant["prix"];
                ?>
            <div class="unit-price"> <?php echo " $prix $"?> </div>
            <div class="menu_divider" style="padding-top: 15px;"></div>
            <div class="side-labels"> Quantité à acheter / vendre :</div>
            <input class="quantity-input" name="numberInput" id="numberInput" oninput="displayNumber()" type="number" value="0" min="0" max="1000000" style="padding-top : 10px;
            padding-bottom : 10px;"> </input>
            <div class="side-labels" style="padding-top: 15px;"> Prix total : </div>
            <div class="full-price" id="display"></div>
              <label><input type="hidden" name="ID_Action" value="<?php echo"$ID_Action"?>"></label>
              <label><input type="hidden" name="unit-price" value="<?php echo"$prix"?>"></label>
          </form>
          <script>
            function displayNumber() {
              const solde = <?php echo $dataUser['soldeJoueur'] ?>;
              const number = document.getElementById('numberInput').value;
              const unit_price = <?php echo $prix; ?>;
              const final_price = number * unit_price;
              const final_price_rounded = final_price.toFixed(2);
              document.getElementById('display').innerHTML = `${final_price_rounded} $`;
                if (final_price_rounded > solde) {
                    document.getElementById('display').style.color = 'red';
                }
                else{
                    document.getElementById('display').style.color = 'white';
                }
              return [number, unit_price]
            }
          </script>
        </div>
      </div>
    </div>
    <?php
    $stockAmount = $bdd->prepare("SELECT SUM(nombreAction) FROM actionpossede WHERE ID_Action = ? AND ID_User = ?");
    $stockAmount -> execute(array($ID_Action, $dataUser['ID_User']));
    $max_sell_amount = $stockAmount -> fetch();
    ?>
    <div class="player-info-bar">
      <div class="infos"> Joueur : <?php echo $dataUser['pseudo'] ?> </div>
      <div class="infos"> Solde : <?php echo $dataUser['soldeJoueur'] ?> $ </div>
      <div class="infos"> Nombre de <?php echo $nameAction ?>  possédé : <?php echo $max_sell_amount[0]?> </div>
    </div>

    <script>
        function Reload() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function () {
            };
            xhttp.open("GET", "nouveauTour.php", true);
            xhttp.send();
            location.reload();
        }
        setInterval(Reload, 120000);
    </script>
  </body>
</html>