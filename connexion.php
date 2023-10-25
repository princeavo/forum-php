<?php 
    $erreur =$succes=  null;
    $title = "Connexion";
    $submit_button_name = "Se connecter";
    require_once "post_vars.php";
    //Si le formulaire est soumis
    if(isset($_POST["inscription"])){
        //Si tous les champs sont remplis
        if(!empty($_POST["email"]) && !empty($_POST["password"])){
            if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
                try{
                    $email = htmlspecialchars($_POST["email"]);
                    $password = htmlspecialchars($_POST["password"]);

                    $verification = $pdo->prepare("SELECT * FROM users WHERE email= :email");
                    $verification->bindParam(":email", $email);
                    $verification->execute();
                    $resultat = $verification->fetchAll(PDO::FETCH_ASSOC);
                    $verification->closeCursor();
                    
                    if($resultat){
                        $dbPass = $resultat[0]["password"];
                        if(password_verify($password,$dbPass)){
                            session_start();
                            $_SESSION['connection'] = 1;
                            $_SESSION["nom"] = $resultat[0]["nom"];
                            $_SESSION["email"] = $resultat[0]["email"];
                            $id = $resultat[0]["id"];
                            $actif = 1;
                            $mise_a_jour = $pdo->prepare("UPDATE users SET actif = :actif WHERE id = :id");
                            $mise_a_jour->bindParam(":actif",$actif);
                            $mise_a_jour->bindParam(":id",$id);
                            $mise_a_jour->execute();
                            header("Location:index.php");
                        }else{
                            $erreur = "Identifiants invalides";
                        }
                    }else{
                        $erreur = "Identifiants invalides";
                    }
                    
                }catch(Exception $e){
                    $erreur = "Connection échouée.Veuillez réessayer";
                } 
            }else{
                //Le mail n'est pas valide
                $erreur = "L'adresse mail n'est pas valide";
            }
        }else{
            $erreur = "Tous les champs ne sont pas remplis";
        }
    }
?>
<?php require_once "html.php"; ?>