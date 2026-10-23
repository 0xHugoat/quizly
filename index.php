<?php


session_start();

require_once('connexion.php');


if (!isset($_SESSION['user_id'])) {
    header('Location: inscription_connexion.php');
    exit();
}

$id = $_SESSION['user_id'];

$sql = "SELECT * FROM quizzes";

$stmt = $db->query($sql);
$quizzes = $stmt->fetchAll();

$stmt = $db->prepare('SELECT pseudo FROM users WHERE id = ?');
$stmt->execute([$id]);
$pseudo = $stmt->fetchColumn();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizly</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur Quizly <?= htmlspecialchars($pseudo, ENT_QUOTES, 'UTF-8') ?></h1>
    </header>
    <main>
        <h3 class="titrealaune">Quiz à la une :</h3>
        <div class="quizz-container">
            <?php foreach ($quizzes as $quizze): ?>
                <div class="quizz-box">
                    <h3><?= htmlspecialchars($quizze['title']) ?></h3>
                    <p><?= htmlspecialchars($quizze['description']) ?></p>
                    <a href="quizze.php?quiz_id=<?= (int) $quizze['id'] ?>">
                        Commencer le quiz
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </main>
    <div class="deconnexion">
        <form action="deconnexion.php" method="post">
            <button type="submit">Se deconnecter</button>
        </form>
    </div>
</body>
</html>