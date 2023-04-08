<?php
  session_start();
  require_once 'bdd.php';
  //include_once('remember.php');

  if(isset($_SESSION['user'])){
    $requUser = $bdd->prepare('SELECT pseudo, soldeJoueur, ID_User FROM user WHERE token = ?');
    $token = $_SESSION['user'];
    $requUser->execute(array($token));
    $dataUser = $requUser->fetch();
  }else{header('Location: deconnexion.php');}
?>

<!DOCTYPE html>

<html>
  <head>
    <metacharset="utf-8">
    <title>Stocks</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <link rel="stylesheet" href="../static/style/stock.css">
    <script src="../js/graph.js"></script>
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
          <li><a href="profile.php">Profil</a></li>
          <li><a href="#">Historique</a></li>
          <li><a href="amis.php">Amis</a></li>
          <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
      </div>
    </nav>
    <div class="menu_divider"></div>


    <nav class="sidenav">
        <ul>
            <li class="dropdown"><a class="main_drop" href="#indices">Indices</a>
                <ul>
                    <li> <a href="#">CAC 40</a> </li>
                    <li> <a href="#">NASDAQ</a> </li>
                    <li> <a href="#">S&P 500</a> </li>
                    <li> <a href="#">EURO STOXX 50</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#forex">Forex</a>
                <ul>
                    <li> <a href="#">EUR/USD</a> </li>
                    <li> <a href="#">USD/JPY</a> </li>
                    <li> <a href="#">USD/CAD</a> </li>
                    <li> <a href="#">EUR/GBP</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#crypto">Crypto</a>
                <ul>
                    <li> <a href="#">Bitcoin</a> </li>
                    <li> <a href="#">Ethernum</a> </li>
                    <li> <a href="#">Tether</a> </li>
                    <li> <a href="#">BNB</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#action">Actions</a>
                <ul>
                    <li> <a href="#">AAPL</a> </li>
                    <li> <a href="#">META</a> </li>
                    <li> <a href="#">AMZN</a> </li>
                    <li> <a href="#">NVDA</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
            <li class="dropdown"><a class="main_drop" href="#taux-obligation">Taux & Obligations</a>
                <ul>
                    <li> <a href="#">French Bond</a> </li>
                    <li> <a href="#">Italian Bond</a> </li>
                    <li> <a href="#">German Bond</a> </li>
                    <li> <a href="#">United States Bond</a> </li>
                </ul>
            </li>
            <div class="menu_divider"></div>
        </ul>
    </nav>


      <div class="page-content">
      <div class="trading-panel">
        <div class="graphes-gauche">
          <div class="graphique_main">
            <?php
            include 'update_graph.php';
              $data_amount = 5;
              $data_array = constructionTableau($bdd, $data_amount);

                $requHistoriquePrix = $bdd->prepare("SELECT prix FROM historiqueaction WHERE ID_Action = ? ORDER BY mois DESC LIMIT 2");
                $requHistoriquePrix -> execute(array(2));
                $price1_raw = $requHistoriquePrix->fetch();
                $price2_raw = $requHistoriquePrix->fetch();
                $price1 = $price1_raw["prix"];
                $price2 = $price2_raw["prix"];

              $percent_change = (($price1 - $price2) / $price2) * 100.0;
              $percent_change_rounded = number_format($percent_change, 1);
            ?>

            <div class="bandeau-infos-trade"> EUR/USD : Changement du dernier mois : <?php echo "$percent_change_rounded" ?> %</div>

            <div id="MainTrade" class="dim-main-trade"></div>
            <script src="../js/calculate_rsi.js"></script>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <script>

              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart(){

                  const data_array = <?php echo json_encode($data_array); ?>;
                  const data_amount = <?php echo json_encode($data_amount); ?>;
                  const data = google.visualization.arrayToDataTable(data_array, true);

                  var options = {
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
          <form class="achat-vente" method="post" action="">
            <div class="achat-vente-button">
              <button type="submit" name="buyButton" class="achat-button"> Acheter </button>
              <button type="submit" name="sellButton" class="sell-button"> Vendre </button>
            </div>
            <div class="menu_divider" style="margin-bottom: 10px;"></div>
            <div>
            <div class="side-labels">Prix à l'unité :</div>
                <?php
                $prix_courrant_req = $bdd->prepare('SELECT prix FROM historiqueaction WHERE mois = (SELECT MAX(mois) FROM historiqueaction)');
                $prix_courrant_req->execute();
                $prix_courrant = $prix_courrant_req->fetch();
                $prix = $prix_courrant["prix"];
                ?>
            <div class="unit-price"> <?php echo " $prix $"?> </div>
            <div class="menu_divider" style="padding-top: 15px;"></div>
            <div class="side-labels"> Quantité à acheter / vendre :</div>
            <input class="quantity-input" name="numberInput" id="numberInput" oninput="displayNumber()" type="number" value="0" min="0" max="1000000" style="padding-top : 10px;
            padding-bottom : 10px;"> </input>
            <div class="side-labels" style="padding-top: 15px;"> Prix total à payer : </div>
            <div class="full-price" id="display"></div>
          </form>
          <?php 
            if(isset($_POST['buyButton'])){ 
              $numberInput = $_POST['numberInput'];
              $moneyCalc = $numberInput*$prix;
              if($moneyCalc <= $dataUser['soldeJoueur']){

                $new_solde_joueur = $dataUser["soldeJoueur"] - $moneyCalc;
                $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE token = ?");
                $sql_money_update->execute(array($new_solde_joueur, $_SESSION['user']));
                if($numberInput != 0){
                  $sql_action_possede = $bdd->prepare("INSERT INTO actionpossede(ID_Action,ID_User, nombreAction, prix_achat) VALUES (?,?,?,?)");
                  $sql_action_possede->execute(array(2, $dataUser['ID_User'], $numberInput, $prix));
                }

                $dataUser['soldeJoueur'] = $new_solde_joueur;
              }
            }
          ?>
          <script>
            function displayNumber() {
              const number = document.getElementById('numberInput').value;
              const unit_price = <?php echo $prix; ?>;
              const final_price = number * unit_price;
              const final_price_rounded = final_price.toFixed(2);
                console.error(final_price_rounded)
              document.getElementById('display').innerHTML = `${final_price_rounded} $`;
              return [number, unit_price]
            }
          </script>
        </div>
      </div>
    </div>
    <div class="player-info-bar">
      <div class="infos">
        Joueur : <?php echo $dataUser['pseudo'] ?>
      </div>
      <div class="infos">
        Solde : <?php echo $dataUser['soldeJoueur'] ?> $
      </div>

      <?php 
        $reqDernierAchat = $bdd->prepare("SELECT nombreAction, prix_achat, nomAction FROM actionpossede INNER JOIN dataaction ON dataaction.ID_Action = actionpossede.ID_Action WHERE ID_User = ? ORDER BY transaction_date DESC LIMIT 1");
        $reqDernierAchat -> execute(array($_SESSION['user']));
        $dernierAchat = $reqDernierAchat->fetch();
      
      if ($dernierAchat){
      ?>
        <div class="infos">
          Dernier Stock Acheté : <?php echo $dernierAchat['nombreAction'] ?> <?php echo $dernierAchat['nomAction'] ?> à <?php echo $dernierAchat['prix_achat'] ?>$ l'unité
        </div>
      <?php 
      }
      ?>
    </div>
  </body>
</html>