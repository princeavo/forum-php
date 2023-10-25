<?php

session_start();
require_once "post_vars.php";
$email = $_SESSION["email"];
$actif = 0;
$infos = $pdo->prepare("UPDATE users SET actif = :actif WHERE email = :email");
$infos->bindParam(':email',$email );
$infos->bindParam(':actif',$actif);
$infos->execute();
$infos->closeCursor();

unset($_SESSION);
session_destroy();
header("location:connexion.php");