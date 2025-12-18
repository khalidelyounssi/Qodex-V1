<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/Database.php';
require_once '../classes/Category.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    $category = new Category($db);

    $category->id = $_GET['id'];
    $category->id_enseignant = $_SESSION['user_id']; 

    if ($category->delete()) {
        header("Location: ../enseignant/dashboard.php?msg=deleted");
    } else {
        header("Location: ../enseignant/dashboard.php?error=delete_failed");
    }
} else {
    header("Location: ../enseignant/dashboard.php");
}
?>