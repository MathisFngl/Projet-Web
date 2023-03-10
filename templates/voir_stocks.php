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
          <li><a href="login.php">Déconnexion</a></li>
        </ul>
      </div>
    </nav>
    <div class="menu_divider"></div>
      <div class="sidenav">
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
      </div>



      <div class="page-content">

      <div class="graphes-gauche">

        <div class="graphique_main">
          <div class="bandeau-infos-trade"> EUR/USD : Changement du dernier mois : <?php $a = 25; echo " +$a%"; ?></div>
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <div id="MainTrade" class="dim-main-trade"></div>
          <script>
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
              var data = google.visualization.arrayToDataTable([
                  ['1', 20, 28, 38, 45],
                  ['2', 31, 38, 55, 66],
                  ['3', 50, 55, 77, 80],
                  ['4', 77, 77, 66, 50],
                  ['5', 68, 66, 22, 15],
                  ['6', 68, 22, 12, 15],
                  ['7', 9, 12, 41, 15],
                  ['8', 29, 41, 39, 45],
                  ['9', 68, 39, 73, 85],
                  ['10', 29, 73, 108, 110],
                  ['11', 98, 108, 159, 183],
                  ['12', 108, 159, 148, 164],], true);

                var options = {
                  legend:'none',
                  candlestick: {
                        fallingColor: { strokeWidth: 0, fill: '#a52714' },
                        risingColor: { strokeWidth: 0, fill: '#0f9d58' }},
                  backgroundColor : { strokeWidth: 0, fill: '#212b36' },
                  chartArea: {'width': '90%', 'height': '85%'},
                };

                var chart = new google.visualization.CandlestickChart(document.getElementById('MainTrade'));

                chart.draw(data, options);
              }
          </script>
        </div>

        <div class="graphique_rsi">
          <div class="bandeau-infos-trade"> RSI (Relative Strenght Index): <?php $a = 25; echo " +$a%"; ?></div>
            <div id="RSI" class="dim-RSI-trade"></div>
            <script>
                google.charts.load('current', {packages: ['corechart', 'line']});
                google.charts.setOnLoadCallback(drawBasic);

                function drawBasic() {

                  var data = new google.visualization.DataTable();
                  data.addColumn('number', 'X');
                  data.addColumn('number', 'Value');

                  data.addRows([
                    [0, 0], [1, 10], [2, 23], [3, 17], [4, 18], [5, 9],
                    [6, 11], [7, 27], [8, 33], [9, 40], [10, 32], [11, 35]
                  ]);

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
                }
              </script>
        
              </div>  
        </div>
      <div class="graphe-droite">
        <div class="achat-vente-button">
          <button class="achat-button"> Achat </button>
          <button class="sell-button"> Vente </button>
        </div>
        <div>
        <div class="price-label">Prix :</div>
        <div class="price"> <?php $a = 1000; echo " $a $"?> </div>
      </div>
    </div>
  </body>
</html>