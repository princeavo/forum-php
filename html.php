<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <style>
            body{
                display:flex;
                align-items : center;
                justify-content:center;
                padding:20px;
            }
            #container{
                width:600px;
                height:400px;
                display:flex;
                align-items : center;
                justify-content:center;
                margin-top:10%;
                background-color:#768495;
                padding:20px;
                box-shadow: 0 0 10px #957685;
            }
            form{
                display:flex;
                justify-content:space-around;
                flex-direction:column;
                width:70%;
                margin:auto;
                overflow:hidden;
            }
            input,button{
                margin:20px auto;
                width:90%;
                padding:10px;
                outline:0;
                border:none;
            }
            input:focus{
                box-shadow:0 0 15px #425134;
            }
            p a {
                padding:10px;
                margin:10px;
            }
        </style>
    </head>
    <body>
        <div id="container">
            <form action="" method="POST">
                <?php if($erreur) echo $erreur; ?>
                <?php if($succes) echo $succes; ?>
                <?php if($title != "Connexion"): ?>
                    <input type="text" name="nom" placeholder="Entrez votre nom ici" autocomplete="off" value="<?php if($erreur) echo htmlspecialchars($_POST['nom']) ?>">
                <?php endif; ?>
                <input type="email" name="email" placeholder="Entrez votre adresse mail ici" autocomplete="off" value="<?php if($erreur) echo htmlspecialchars($_POST['email']) ?>">
                <input type="password" name="password" placeholder="Entrez votre mot de passe ici" autocomplete="off" value="<?php if($erreur) echo htmlspecialchars($_POST['password']) ?>">
                <button type="submit" name="inscription"><?= $submit_button_name ?></button>
                <p><a href="connexion.php">Se connecter</a> <a href="inscription.php">S'inscrire</a> <a href="index.php">Acceder&nbsp;à&nbsp;l'acceuil</a></p>
            </form>
        </div>
    </body>
</html>