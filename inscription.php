<?php 

    $title = "Inscription";
    $submit_button_name = "S'inscrire";
    $erreur = $succes =  null;
    //Si le formulaire est soumis
    if(isset($_POST["inscription"])){
        //Si tous les champs sont remplis
        if(!empty($_POST["email"]) && !empty($_POST["password"]) && !empty($_POST["nom"])){
            if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){

               require_once "post_vars.php";
                $nom = htmlspecialchars($_POST["nom"]);
                $email = htmlspecialchars($_POST["email"]);
                $password = htmlspecialchars($_POST["password"]);

                try{
                    $verification = $pdo->prepare("SELECT * FROM users WHERE email= :email");
                    $verification->bindParam(":email", $email);
                    $verification->execute();
                    $resultat = $verification->fetchAll(PDO::FETCH_ASSOC);
                    if($resultat){
                        $erreur = "L'adresse entrée est déjà enrégistrée.Veuilez essayer avec une autre adresse";
                    }else{
                        try{
                            $insertion = $pdo->prepare("INSERT INTO users (nom,email,password,actif) VALUES (:nom,:email,:password,:actif) ");
                            $password =password_hash($password, PASSWORD_DEFAULT,["cost"=>"12"]);
                            $actif=0;
                            $insertion->bindParam(':email',$email);
                            $insertion->bindParam(':password',$password);
                            $insertion->bindParam(':nom',$nom);
                            $insertion->bindParam(':actif',$actif);
                            $insertion->execute();
                            $insertion->closeCursor();
                            header("Location:inscription.php?compte=attente");
                        }catch(Exception $e){
                            $erreur = "Erreur lors de l'enrégistrement";
                        } 
                    }
                    $verification->closeCursor();
                }catch(Exception $e){
                    $erreur = "Erreur lors de l'enrégistrement.Veuillez réessayer";
                }
                

               
            }else{
                //Le mail n'est pas valide
                $erreur = "L'adresse mail n'est pas valide";
            }
        }else{
            $erreur = "Tous les champs ne sont pas remplis";
        }
    }
    if(isset($_GET["compte"]) && $_GET["compte"]=="attente"){
        $succes = "Inscription réussie";
    }
?>
<?php require_once "html.php"; ?>