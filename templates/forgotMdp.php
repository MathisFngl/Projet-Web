<?php
  session_start();
  require_once 'bdd.php';
  if(isset($_GET['section'])){
    $section = htmlspecialchars($_GET['section']);
  }else{ $section="";}
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="UTF-8">
    <title>MdpOublie</title>
    <!--<link rel="stylesheet" href="../static/style/style.css">-->
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
                    <li class="active"><a href="#">Acceuil</a></li>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php">S'inscrire</a></li>
                    <li><a href="info.php">Info</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
        </nav>
        <?php 
        if(isset($_GET['reg_err']))
            {
                $err = htmlspecialchars($_GET['reg_err']);

                switch($err)
                {
                    case 'password':
                    ?>
                        <div class="h2-register">
                            <strong>Erreur</strong> mot de passe différent
                        </div>
                    <?php
                    break;

                    case 'email':
                    ?>
                        <div class="h2-register">
                            <strong>Erreur</strong> email non valide
                        </div>
                    <?php
                    break;
                }}
                ?>
    <div class="form-value">
    <h2>Mot de Passe Oublié</h2>
    <?php
      if($section == "code"){ ?>
       <div class="php"><p>Inscrivez le code reçu sur <? echo $_SESSION['email']; ?></p></div>
        <form action="forgotMdp_bdd.php" method="post">
          <div class="inputbox">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="text" name="codeSubmit" required >
            <label for="code">Code</label>
          </div>
          <button type="submit" class="buttonLog">Vérifier le code</button>
        </form>
       
    <?php } else if($section == "resetMdp"){ ?>
        <div class="php"><p>Inscrivez un nouveau mot de passe></p></div>
        <form action="forgotMdp_bdd.php" method="post">
          <div class="inputbox-register">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="newPassword" required>
            <label for="">Mot de passe</label>
          </div>
          <div class="inputbox-register">
            <ion-icon name="lock-closed-outline"></ion-icon>
            <input type="password" name="newPassword2" required>
            <label for="">Confirmation mot de passe</label>
          </div>
          <button type="submit" class="buttonLog">Changer le mot de passe</button>
        </form>
    <?php } else { ?>
      <form action="forgotMdp_bdd.php" method="post">
        <div class="inputbox">
          <ion-icon name="mail-outline"></ion-icon>
          <input type="email" name="email" required >
          <label for="email">Email</label>
        </div>
        <button type="submit" class="buttonLog">Envoyer un code</button>
      </form>
      
    </div>
    <?php } ?>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>
