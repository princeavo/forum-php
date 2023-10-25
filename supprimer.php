<?php
    session_start();
    if(!isset($_SESSION['connection']) || $_SESSION['connection'] != 1 || !isset($_SESSION["nom"]) || empty($_SESSION["nom"]))
        header('Location:connexion.php');
    if(!isset($_GET["id"]) || empty($_GET["id"])){
        header('Location:index.php');
        die();
    }
    $id = htmlspecialchars($_GET["id"]);
    require_once "post_vars.php";
    try{
        $request = $pdo->prepare("DELETE FROM posts WHERE id = :id");
        $request->bindParam(":id",$id );
        $request->execute();
        $request->closeCursor();
    }catch(Exception $e){
        echo "Error: while deleting message " ;
    }
    header("Location:index.php");
    
