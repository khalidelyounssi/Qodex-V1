<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/Database.php';
require_once '../classes/Quiz.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $quiz = new Quiz($db);

    $quiz->id = $_GET['id'];
    $quiz->id_enseignant = $_SESSION['user_id'];

    if ($quiz->delete()) {
        header("Location: ../enseignant/dashboard.php?msg=quiz_deleted#quiz");
    } else {
        header("Location: ../enseignant/dashboard.php?error=delete_failed#quiz");
    }
} else {
    header("Location: ../enseignant/dashboard.php");
}
?>