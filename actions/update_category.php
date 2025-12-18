<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php"); exit;
}

require_once '../config/Database.php';
require_once '../classes/Category.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $category = new Category($db);

    $category->id = $_POST['id'];
    $category->nom = $_POST['nom'];
    $category->description = $_POST['description'];
    $category->id_enseignant = $_SESSION['user_id'];

    if ($category->update()) {
        header("Location: ../enseignant/dashboard.php?msg=updated#categories");
    } else {
        header("Location: ../enseignant/dashboard.php?error=update_failed");
    }
}
?>