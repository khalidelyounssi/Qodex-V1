<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/Database.php';
require_once '../classes/Quiz.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['titre']) && !empty($_POST['categorie_id'])) {
        
        $database = new Database();
        $db = $database->getConnection();
        $quiz = new Quiz($db);

        $quiz->titre = $_POST['titre'];
        $quiz->description = $_POST['description'];
        $quiz->id_categorie = $_POST['categorie_id'];
        $quiz->id_enseignant = $_SESSION['user_id'];

        if ($quiz->create()) {
            header("Location: ../enseignant/dashboard.php?success=quiz_created#quiz");
        } else {
            header("Location: ../enseignant/dashboard.php?error=quiz_failed#quiz");
        }
    } else {
         header("Location: ../enseignant/dashboard.php?error=empty_fields#quiz");
    }
} else {
    header("Location: ../enseignant/dashboard.php");
}
?>