<?php
    require_once "post_vars.php";
    $erreur =$succes= $value =  null;
    $title = "Acceuil";
    $submit_button_name = "Publier votre question";
    session_start();
    if(!isset($_SESSION['connection']) || $_SESSION['connection'] != 1 || !isset($_SESSION["nom"]) || empty($_SESSION["nom"]))
        header('Location:connexion.php');
    if(isset($_POST["publier"]) ){
        if(!empty($_POST["message"])){
            $message = nl2br(htmlspecialchars($_POST["message"]));
            try{
                $request = $pdo->prepare("INSERT INTO posts (nom,email, message,date) VALUES (:nom,:email, :message , :date)");
                $request->bindParam(':nom', $_SESSION["nom"]);
                $request->bindParam(':message' ,$message);
                // $date = date("Y-m-d H:i:s");
                $date = date("d/m/Y H:i:s");
                $request->bindParam(':date',$date);
                $request->bindParam(':email', $_SESSION["email"]);
                $request->execute();
                $erreur = var_dump($request);
                $succes = "Message envoyé avec succès";
                header('location:index.php');
            }catch(Exception $e){
                $erreur = "Error while sendind message " ;
            }
        }
    }
    if(isset($_POST["modifier"])){
        if(!empty($_POST["message"])){
            $message = nl2br(htmlspecialchars($_POST["message"]));
            $id = htmlspecialchars($_GET["id"]) ;
            try{
                $request = $pdo->prepare("UPDATE posts SET message= :message WHERE id = :id");
                $request->bindParam(':id', $id);
                $request->bindParam(':message',$message);
                $request->execute();
                $succes = "Message modifié avec succès";
                $request->closeCursor();
            }catch(Exception $e){
                $erreur = "Error while updating message " ;
            }
            header('location:index.php');
        }else{
            $erreur = "Veuillez entrer un message non vide " ;
        }
    }
    if(isset($_POST["repondre"])){
        if(!empty($_POST["reponse"])){
            try{
                $reponse = nl2br(htmlspecialchars($_POST["reponse"]));
                $id = (int)$_POST["id"];
                $nom = htmlspecialchars($_POST["nom"]);
                $insertion = $pdo->prepare("INSERT INTO `reponses` (`id`, `nom`, `reponse`) VALUES (:id, :nom, :reponse) ");
                $insertion->bindParam(':id', $id);
                $insertion->bindParam(':nom', $nom);
                $insertion->bindParam(':reponse', $reponse);
                $insertion->execute();
                $insertion->closeCursor();
            }catch(Exception $e){

            }
        }
        header("Location:index.php");
    }
    if(isset($_GET["id"]) && !empty($_GET["id"])){
        try{
            $id = htmlspecialchars($_GET["id"]) ;
            $verification = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
            $verification->bindParam(':id',$id);
            $verification->execute();
            $resultat = $verification->fetchAll(PDO::FETCH_ASSOC);
            $verification->closeCursor();
            if($resultat[0]["nom"] == $_SESSION["nom"]){
                if($resultat){
                    $submit_button_name = "Modifier votre question";
                    $value =  str_replace("<br />","",$resultat[0]["message"]);
                }else{
                    $erreur = "Nous n'avons pas pu modifier votre message";
                }
            }

        }catch(Exception $e){
            $erreur = "Erreur lors de la modification du message";
        }
       
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title><?= $title ?></title>
        <style>
            *{
                margin:0:
                padding:0;
            }
            body{
                padding:20px;
            }
            #container{
                // width:100%;
                height:350px;
                display:flex;
                align-items : center;
                justify-content:center;
                margin:20px;
                background-color: #758476;
                padding:20px;
                box-shadow: 0 0 10px #957685;
            }
            .container{
                // width:100%;
                display:flex;
                align-items : center;
                justify-content:space-around;
                flex-wrap:wrap;
                margin:20px;
                background-color: #859476;
                padding:20px;
                box-shadow: 0 0 10px #957685;
            }.question{
                padding:10px;
                margin:5px;
                background-color: #917382;
                box-shadow: 0 0 20px #739182;
                border-radius:10px;
                width:100%;
            }
            #reponse{
                background-color: #958679;
                border:2px solid #958679;
            }
            form{
                display:flex;
                justify-content:space-around;
                flex-direction:column;
                width:70%;
                margin:auto;
                overflow:hidden;
            }
            textarea,button{
                margin:20px auto;
                width:90%;
                padding:10px;
                outline:0;
                border:none;
                resize:none;
            }
            input:focus{
                box-shadow:0 0 15px #444;
            }
            a{
                text-decoration:none;
            }
        </style>
    </head>
    <body>
        <h1>Welcome <?=$_SESSION["nom"]?></h1>
        <a href="logout.php">Se deconnecter</a>
        <?php
            try{
                $request = $pdo->prepare("SELECT * FROM `posts` ORDER BY `posts`.`date` ASC");
                $request->execute();
                $liste = $request->fetchAll(PDO::FETCH_ASSOC);
                $request->closeCursor();
            }catch(Exception ){
                echo "Une erreur s'est produite veuillez actualiser la page...";
            }
        ?>
        <div class="container">
        <?php if(!$liste) echo "Les messages s'affichent ici"; ?>
        <?php foreach($liste as $messages) : ?>
            <div class="question">
                <?php 
                    $email = $messages["email"];
                    $infos = $pdo->prepare("SELECT * FROM users WHERE email= :email");
                    $infos->bindParam(':email',$email );
                    $infos->execute();
                    $actif = ($infos->fetchAll(PDO::FETCH_ASSOC))[0]["actif"];
                    $actif = ($actif =="1")?"actif":"";
                    $infos->closeCursor();
                ?>
                <p><strong><?php if($messages["nom"] == $_SESSION["nom"] && $messages["nom"]!="Administrateur") echo "Moi"; else echo $messages["nom"];?></strong><em>  <?=$actif?></em><p>
                <p><strong><?php if($messages["nom"] == "Administrateur") echo "Administrateur";?></strong><em>  <?=$actif?></em><p>
                    <?php echo $messages["message"]?>
                    <p> <strong>écrit le </strong><i><?php echo $messages["date"];?></i></p>
                    <?php if($messages["nom"] == $_SESSION["nom"] || $_SESSION["nom"] == "Administrateur"):?>
                        <p><a href="index.php?id=<?=$messages['id']?>#message">Modifer le message</a></p>
                        <p><a href="supprimer.php?id=<?=$messages['id']?>">Supprimer le message</a></p>
                    <?php endif;?>
                    <div id="reponse">
                        <!-- On affiche les réponses à cette question ici  -->
                        <?php 
                            try{
                                $id = $messages["id"];
                                $request = $pdo->prepare('SELECT * FROM reponses WHERE id = :id');
                                $request->bindParam(':id',$id);
                                $request->execute();
                                $reponses = $request->fetchAll(PDO::FETCH_ASSOC);
                                $request->closeCursor();
                            }catch(Exception ){

                            }
                        ?>
                        <?php if($reponses): ?>
                            Les réponses 
                            <?php foreach($reponses as $reponse): ?>
                                <div>
                                    <h3><?=$reponse["nom"]?></h3>
                                    <p><?=$reponse["reponse"]?></p>
                                </div>
                            <?php endforeach ;?>
                            Fin des réponses
                        <?php else: ?>
                            <p>Il n'y as pas de réponses à votre question.Soiyez le premier à répondre!</p>
                        <?php endif; ?>
                        <form action="" method="POST">
                            <textarea id="reponses" name="reponse" placeholder="Répondre à ce message" rows="10"></textarea>
                            <input type="hidden" name="nom" value="<?php echo $_SESSION['nom']; ?>" />
                            <input type="hidden" name="id" value="<?php echo $messages['id']; ?>" />
                            <button type="submit" name="repondre" >Répondre</button>
                        </form>
                    </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div id="container">
            <form action=" " method="POST">
                <?php if($erreur) echo $erreur; ?>
                <?php if($succes) echo $succes; ?>
                <textarea  id = "message" name="message" placeholder="Entrez votre publication ici" autocomplete="off" rows = "10" ><?=$value?></textarea>
                <?php if($submit_button_name =="Modifier votre question" ) $name = "modifier"; else $name = "publier" ; ?>
                <button type="submit" name="<?php echo $name;?>" ><?= $submit_button_name ?></button>
                <?php if($submit_button_name =="Modifier votre question"): ?>
                    <a href="index.php">Anuler</a>
                <?php endif; ?>
            </form>
        </div>
        Ci-dessous la liste des utilisateurs connectés 
        <?php 
            $usr = $pdo->prepare("SELECT * FROM `users` WHERE actif = 1");
            $usr->execute();
            $users = $usr->fetchAll(PDO::FETCH_ASSOC);
            $usr->closeCursor();
        ?>
        <?php foreach($users as $user) : ?>
            <p><?= $user["nom"] ?></p>
        <?php endforeach ; ?>
    </body>
</html>