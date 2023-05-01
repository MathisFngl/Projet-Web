<?php
    session_start();
    require_once 'bdd.php';
    require('remember.php');

    $photo = $bdd->prepare('SELECT photo,ID_Photo FROM photo');
    $photo->execute();
    
?>

<!DOCTYPE html>
<html>
  <head>
    <metacharset="utf-8">
    <title>Register</title>
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
                <li><a href="login.php">Connexion</a></li>
                <li class="active"><a href="#">S'inscrire</a></li>
                <li><a href="info.php">Info</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </div>
    </nav>
    <div class="menu_divider"></div>
    <section class="form-box-register">
    <?php 
        if(isset($_GET['reg_err']))
            {
                $err = htmlspecialchars($_GET['reg_err']);

                switch($err)
                {
                    case 'password':
                    ?>
                        <div class="e-register">
                            <strong>Erreur</strong> mot de passe différent
                        </div>
                    <?php
                    break;

                    case 'email':
                    ?>
                        <div class="e-register">
                            <strong>Erreur</strong> email non valide
                        </div>
                    <?php
                    break;

                    case 'pseudo':
                        ?>
                            <div class="e-register">
                                <strong>Erreur</strong> pseudo non valide
                            </div>
                        <?php
                        break;

                    case 'already':
                    ?>
                        <div class="e-register">
                            <strong>Erreur</strong> compte deja existant
                        </div>
                    <?php 
                    }
                }
                ?>
        <div class="form-value-register">
            <form action="register_bdd.php" method="post" enctype="multipart/form-data">
                <h2 class="h2-register">Inscription</h2>
                <div class="inputbox-register">
                <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="pseudo" required>
                    <label for="">Pseudo</label>
                </div>
                <div class="inputbox-register">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" required>
                    <label for="">Email</label>
                </div>
                <div class="inputbox-register">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password" required>
                    <label for="">Mot de passe</label>
                </div>
                <div class="inputbox-register">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password2" required>
                    <label for="">Confirmation mot de passe</label>
                </div>
                <div class="modal-container-register">
                    <div class="modal-register">
                        <button class="close-modal-register modal-trigger-register">X</button>
                        <?php 
                            foreach($photo as $image){
                                echo '<img src="data:image/jpeg;base64,'.base64_encode($image['photo']).'" alt="photo de profil" class="modal-trigger-register" onclick="selectPhoto('.$image['ID_Photo'].')">';
                            }
                        ?>
                    </div>
                    <input type="hidden" id="photo" name="photo" required>
                 </div>   
                <input type="button" class="modal-btn-register modal-trigger-register" value="Selectionner une photo" required>
                <button type="submit" class="buttonLog-register">S'inscrire</button>
            </form>
            <script>
                function selectPhoto(photo) {
                // Sélectionnez la photo cliquée
                    var photoInput = document.getElementById("photo");
                    photoInput.value = photo;
                }
            </script>
        </div>
    </section>
    <script src="../js/app.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>