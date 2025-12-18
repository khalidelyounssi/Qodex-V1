<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php"); exit;
}
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    $query = "SELECT * FROM categories WHERE id = ? AND id_enseignant = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$category) die("Catégorie introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier Catégorie</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Modifier la catégorie</h2>
        <form action="../actions/update_category.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
            <label class="block mb-2 font-bold">Nom</label>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($category['nom']); ?>" class="w-full border p-2 mb-4 rounded">
            
            <label class="block mb-2 font-bold">Description</label>
            <textarea name="description" class="w-full border p-2 mb-4 rounded"><?php echo htmlspecialchars($category['description']); ?></textarea>
            
            <div class="flex justify-between">
                <a href="dashboard.php" class="text-gray-500 py-2">Annuler</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Mettre à jour</button>
            </div>
        </form>
    </div>
</body>
</html>