<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php"); exit;
}

require_once '../config/Database.php';
require_once '../classes/Quiz.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $quiz = new Quiz($db);

    $quiz->id = $_POST['id'];
    $quiz->titre = $_POST['titre'];
    $quiz->description = $_POST['description'];
    $quiz->id_categorie = $_POST['categorie_id'];
    $quiz->id_enseignant = $_SESSION['user_id'];

    if ($quiz->update()) {
        header("Location: ../enseignant/dashboard.php?msg=quiz_updated#quiz");
    } else {
        header("Location: ../enseignant/dashboard.php?error=update_failed");
    }
}
?>