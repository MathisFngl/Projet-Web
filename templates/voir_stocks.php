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
          <li><a href="#">Amis</a></li>
          <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
      </div>
    </nav>
    <div class="menu_divider"></div>
    <nav class="sidenav">
      <a href="#indices">Indices</a>
      <div class="menu_divider"></div>
      <a href="#forex">Forex</a>
      <div class="menu_divider"></div>
      <a href="#crypto">Crypto</a>
      <div class="menu_divider"></div>
      <a href="#action">Actions</a>
      <div class="menu_divider"></div>
      <a href="#taux-obligation">Taux & Obligations</a>
      <div class="menu_divider"></div>
    </nav>
      <div class="page-content">
      <div class="trading-panel">
        <div class="graphes-gauche">
          <div class="graphique_main">
            <?php
            include 'update_graph.php';
              $data_amount = 7;
              $data_array = constructionTableau($bdd, $data_amount);
            ?>

            <?php
              /*include 'update_graph.php';
              $data_amount = 11;
              $data_array = constructionTableau($bdd);
              echo '<script>var data_amount = "' . $data_amount . '";</script>';
              echo '<script>var data_array = "' . $data_array . '";</script>';*/
            ?>

            <div class="bandeau-infos-trade"> EUR/USD : Changement du dernier mois : <span id="percentage_general"></span> </div>
            <script>

              var dataPHP = <?php echo json_encode($data_array); ?>;
              var data_array = JSON.parse(dataPHP);

              var percent_change = 0;
                if(data_array[data_amount-1][2] < data_array[data_amount-1][3]){
                  if(data_array[data_amount-1][2] < data_array[data_amount-1][3]){
                    percent_change = ((data_array[data_amount][3] - data_array[data_amount-1][3])/data_array[data_amount-1][3])*100.0;
                  }
                  if(data_array[data_amount-2][2] > data_array[data_amount-2][3]){
                    percent_change = ((data_array[data_amount][3] - data_array[data_amount-1][2])/data_array[data_amount-1][3])*100.0;
                    }
                }
                if(data_array[data_amount-1][2] > data_array[data_amount-1][3]){
                  if(data_array[data_amount-1][2] < data_array[data_amount-1][3]){
                    percent_change = ((data_array[data_amount][2] - data_array[data_amount-1][3])/data_array[data_amount-1][3])*100.0;
                  }
                  if(data_array[data_amount-2][2] > data_array[data_amount-2][3]){
                    percent_change = ((data_array[data_amount][2] - data_array[data_amount-1][2])/data_array[data_amount-1][3])*100.0;
                    }
                }

              const percent_change_rounded = percent_change.toFixed(1);

              document.getElementById("percentage_general").innerHTML = percent_change_rounded + "%";
            </script>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <div id="MainTrade" class="dim-main-trade"></div>
            <script src="../js/calculate_rsi.js"></script>
            <script>

              google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);
              function drawChart(){
                
                const data = google.visualization.arrayToDataTable(data_array, true);

                  var options = {
                    legend:'none',
                    candlestick: {
                          fallingColor: { wickStroke: 'white', strokeWidth: 0, fill: '#a52714' },
                          risingColor: { wickStroke: 'white', strokeWidth: 0, fill: '#0f9d58' }},
                    backgroundColor : { strokeWidth: 0, fill: '#212b36' },
                    colors: ['#f1f1f1'],
                    chartArea: {'width': '90%', 'height': '85%'},
                  };

                  var chart = new google.visualization.CandlestickChart(document.getElementById('MainTrade'));
                  chart.draw(data, options);

                  window.addEventListener('resize', function() {
                    setTimeout(function() {
                    chart.draw(data, options);
                    }, 0);
                  });
                }
                
                const prix_bougies = [];
                for (let i = 0; i < data_amount; i++) {
                  var innerArray = data_array[i];
                  if(innerArray[3] < innerArray[2]){
                    var fourthElement = innerArray[3];
                  }
                  if(innerArray[2] < innerArray[3]){
                    var fourthElement = innerArray[2];
                  }
                  prix_bougies.push(fourthElement);
                }
                  const valeurs_rsi = calculateRSI(prix_bougies, 3);
                  console.log(valeurs_rsi);
            </script>
          </div>
          <div class="graphique_rsi">
            <div class="bandeau-infos-trade"> RSI (Relative Strenght Index): <span id="percentage_rsi"></span> </div>
            <script>
              var percentage_last_rsi = ((valeurs_rsi[data_amount-1] - valeurs_rsi[data_amount-2])/valeurs_rsi[data_amount-2])*100.0;
              var rounded_percentage_last_rsi = percentage_last_rsi.toFixed(1);
              document.getElementById("percentage_rsi").innerHTML = rounded_percentage_last_rsi + "%";

            </script>
              <div id="RSI" class="dim-RSI-trade"></div>
              <script>
                  google.charts.load('current', {packages: ['corechart', 'line']});
                  google.charts.setOnLoadCallback(drawBasic);

                  function drawBasic() {

                    var data = new google.visualization.DataTable();
                    data.addColumn('number', 'X');
                    data.addColumn('number', 'Value');

                    const rows = []
                    for (let i = 0; i < data_amount; i++) {
                      var local_row = [i, valeurs_rsi[i]];
                      rows.push(local_row);
                    }

                    data.addRows(
                      rows
                    );

                    var options = {
                      legend: 'none',
                      backgroundColor: { strokeWidth: 0, fill: '#212b36' },
                      chartArea: { 'width': '90%', 'height': '85%' },
                      vAxis: {
                      viewWindow: {
                            min: 0,
                            max: 100
                            },
                        ticks: [30, 70],
                        gridlines: {
                          color: '#ccc',
                          count: 2
                        }
                      },
                      hAxis: {
                        viewWindow: {
                          min: 0,
                          max: 11
                        },
                        gridlines: {
                          color: 'transparent'
                        }
                      },
                      series: {
                        0: {
                          color: '#fdd835',
                          lineWidth: 1 
                        }
                      }
                    };

                    var chart = new google.visualization.LineChart(document.getElementById('RSI'));

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
            <div class="unit-price"> <?php $unit_price = 3.63; echo " $unit_price $"?> </div>
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
              $moneyCalc = $numberInput*$unit_price;
              if($moneyCalc <= $dataUser['soldeJoueur']){

                $new_solde_joueur = $dataUser["soldeJoueur"] - $moneyCalc;
                $sql_money_update = $bdd->prepare("UPDATE user SET soldeJoueur = ?  WHERE token = ?");
                $sql_money_update->execute(array($new_solde_joueur, $_SESSION['user']));
                if($numberInput != 0){
                  $sql_action_possede = $bdd->prepare("INSERT INTO actionpossede(ID_Action,ID_User, nombreAction, prix_achat) VALUES (?,?,?,?)");
                  $sql_action_possede->execute(array(2, $dataUser['ID_User'], $numberInput, $unit_price));
                }

                $dataUser['soldeJoueur'] = $new_solde_joueur;
              }
            }
          ?>
          <script>
            function displayNumber() {
              const number = document.getElementById('numberInput').value;
              const unit_price = <?php echo $unit_price; ?>;
              const final_price = number * unit_price;
              const final_price_rounded = final_price.toFixed(2);
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
          Dernier Stock Acheté : <?php echo $dernierAchat['nombreAction'] ?> <?php echo $dernierAchat['nomAction'] ?> <?php echo $dernierAchat['prix_achat'] ?>$ unité
        </div>
      <?php 
      }
      ?>
    </div>
  </body>
</html>