<?php
    session_start();
    require_once 'bdd.php';

    include_once('remember.php');
?>
<!DOCTYPE html>

<html>
  <head>
    <metacharset="utf-8">
    <title>LogIn</title>
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
                <li><a href="index.php">Acceuil</a></li>
                <li class="active"><a href="#">Connexion</a></li>
                <li><a href="register.php">S'inscrire</a></li>
                <li><a href="info.php">Info</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </nav>
    <div class="menu_divider"></div>
    <section class="form-box">
    <?php 
        if(isset($_GET['reg_err']))
            {
                $err = htmlspecialchars($_GET['reg_err']);

                switch($err)
                {
                    case 'password':
                    ?>
                        <div class="h2-register">
                            <strong>Erreur</strong> mot de passe incorrect
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

                    case 'already':
                    ?>
                        <div class="h2-register">
                            <strong>Erreur</strong> déja connecté
                        </div>
                    <?php 
                    }
                }
                ?>
        <div class="form-value">
            <form action="login_bdd.php" method="post">
                <h2>Connexion</h2>
                <div class="inputbox">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" required>
                    <label for="">Email</label>
                </div>
                <div class="inputbox">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password" required>
                    <label for="">Mot de passe</label>
                </div>
                <div class="forget">
                    <label for=""><input type="checkbox" name="remember">Se Souvenir | <a href="forgotMdp.php">Mot de passe oublié ?</a></label>
                </div>
                <button type="submit" class="buttonLog">Connexion</button>
                <div class="register">
                    <p>Pas de compte <a href="register.php">S'INSCRIRE</a></p>
                </div>
            </form>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>