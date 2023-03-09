<!DOCTYPE html>

<html>
  <head>
    <metacharset="utf-8">
    <title>Stocks</title>
    <link rel="stylesheet" href="../static/style/style.css">
    <script src="../static/graphe.js"></script>
  </head>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
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
        <a href="#etf">ETF</a>
        <div class="menu_divider"></div>
      </div>
      <div class="page-content">
      <div class="bandeau-infos-trade"> EUR/USD : Changement du dernier mois : 
      <?php
        $a = 25;
        echo " +$a%"; 
      ?>
      </div>
      <div class="trade-body">
        <div class="graphique">
        <canvas  id="myChart"></canvas>
        <script>
          var xValues = [1,2,3,4,5,6,7,8,9,10,11,12];

          new Chart("myChart", {
              type: "line",
              data: {
                  labels: xValues,
                  datasets: [{ 
                  data: [860,1140,1060,1060,1070,1110,1330,2210,7830,2478,1303,2640],
                  borderColor: "red",
                  fill: true
                  }]
              },
              options: {
                  legend: {display: false}
              }
          });
        </script>
        </div>
      </div>
      <div class="bandeau-infos-trade"> RSI (Relative Strenght Index): 
      <?php
        $a = 25;
        echo " +$a%"; 
      ?>
    </div>
  </body>
</html>