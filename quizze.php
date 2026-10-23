<?php
session_start();
require_once 'connexion.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: inscription_connexion.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$quizId = filter_input(INPUT_GET, 'quiz_id', FILTER_VALIDATE_INT);

if (!$quizId) {
    exit('Quiz introuvable.');
}

// Recommencer la partie
if (isset($_GET['restart']) && $_GET['restart'] === '1') {
    unset($_SESSION['parties'][$userId][$quizId]);

    header('Location: quizze.php?quiz_id=' . $quizId);
    exit;
}

// Créer une nouvelle partie si cet utilisateur n'en a pas pour ce quiz
if (!isset($_SESSION['parties'][$userId][$quizId])) {
    $stmt = $db->prepare(
        'SELECT id
         FROM questions
         WHERE quiz_id = ?
         ORDER BY RAND()'
    );
    $stmt->execute([$quizId]);

    $questionIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($questionIds) === 0) {
        exit('Ce quiz ne contient aucune question.');
    }

    $_SESSION['parties'][$userId][$quizId] = [
        'questions' => $questionIds,
        'position' => 0,
        'score' => 0
    ];
}

$partie = &$_SESSION['parties'][$userId][$quizId];

// Traiter la réponse envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionId = $partie['questions'][$partie['position']] ?? null;
    $questionEnvoyee = filter_input(
        INPUT_POST,
        'question_id',
        FILTER_VALIDATE_INT
    );
    $answerId = filter_input(
        INPUT_POST,
        'answer_id',
        FILTER_VALIDATE_INT
    );

    if (
        !$questionId ||
        $questionEnvoyee !== (int) $questionId ||
        !$answerId
    ) {
        exit('Réponse invalide.');
    }

    // Vérifier que la réponse appartient bien à la question actuelle
    $stmt = $db->prepare(
        'SELECT is_correct
         FROM answers
         WHERE id = ? AND question_id = ?'
    );
    $stmt->execute([$answerId, $questionId]);
    $isCorrect = $stmt->fetchColumn();

    if ($isCorrect === false) {
        exit('Cette réponse ne correspond pas à la question.');
    }

    if ((int) $isCorrect === 1) {
        $partie['score']++;
    }

    $partie['position']++;

    // Recharger la page pour afficher la question suivante
    header('Location: quizze.php?quiz_id=' . $quizId);
    exit;
}

// Trouver la question à afficher
$questionId = $partie['questions'][$partie['position']] ?? null;

if ($questionId !== null) {
    $stmt = $db->prepare(
        'SELECT id, statement
         FROM questions
         WHERE id = ?'
    );
    $stmt->execute([$questionId]);
    $question = $stmt->fetch();

    $stmt = $db->prepare(
        'SELECT id, answer_text
         FROM answers
         WHERE question_id = ?'
    );
    $stmt->execute([$questionId]);
    $answers = $stmt->fetchAll();
    shuffle($answers);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizly</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <main>
        <?php if ($questionId === null): ?>
            <h1>Quiz terminé !</h1>

            <p>
                Ton score :
                <?= (int) $partie['score'] ?>
                / <?= count($partie['questions']) ?>
            </p>

            <a href="quizze.php?quiz_id=<?= $quizId ?>&restart=1">
                Rejouer
            </a>

            <a href="./index.php">Retournez au menu</a>
        <?php else: ?>
            <p>
                Question <?= $partie['position'] + 1 ?>
                sur <?= count($partie['questions']) ?>
            </p>

            <h1>
                <?= htmlspecialchars($question['statement'], ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <form method="post">
                <input
                    type="hidden"
                    name="question_id"
                    value="<?= (int) $questionId ?>">

                <?php foreach ($answers as $answer): ?>
                    <button
                        type="submit"
                        name="answer_id"
                        value="<?= (int) $answer['id'] ?>"
                        class="bouton-reponse">
                        <?= htmlspecialchars($answer['answer_text'], ENT_QUOTES, 'UTF-8') ?>
                    </button>
                <?php endforeach; ?>
            </form>
        <?php endif; ?>
    </main>
</body>

</html>