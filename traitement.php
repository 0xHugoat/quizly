
<?php

session_start();
require_once('connexion.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pseudo = trim($_POST['pseudo'] ?? '');

    if ($pseudo === '') {
        exit("Veuillez remplir les champs obligatoires.");
    }

    // Vérifier si le pseudo existe
    $sql = "SELECT * FROM users WHERE pseudo = :pseudo";
    $stmt = $db->prepare($sql);
    $stmt->execute(['pseudo' => $pseudo]);

    $user = $stmt->fetch();

    if (!$user) {
        // Le pseudo n'existe pas : on l'ajoute
        $sql = "INSERT INTO users (pseudo) VALUES (:pseudo)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['pseudo' => $pseudo]);

        // Récupérer l'ID du nouvel utilisateur
        $id = $db->lastInsertId();

    } else {
        // Le pseudo existe déjà
        $id = $user['id'];
    }

    // Connecter l'utilisateur
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
    $_SESSION['pseudo'] = $pseudo;

    // Redirection vers les quiz
    header('Location: index.php');
    exit;
}

?>
