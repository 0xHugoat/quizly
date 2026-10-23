<?php 
session_start();

$_SESSION = [];

session_destroy();

header('Location: inscription_connexion.php');