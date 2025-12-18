<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php"); exit;
}
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

if(isset($_GET['id'])) {
    // جلب معلومات الكويز
    $query = "SELECT * FROM quiz WHERE id = ? AND id_enseignant = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // جلب كل الكاتيكوريات باش نعمرو الـ Select
    $catQuery = "SELECT * FROM categories WHERE id_enseignant = ?";
    $catStmt = $db->prepare($catQuery);
    $catStmt->execute([$_SESSION['user_id']]);
    
    if(!$quiz) die("Quiz introuvable.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Modifier Quiz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Modifier le Quiz</h2>
        <form action="../actions/update_quiz.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $quiz['id']; ?>">
            
            <label class="block mb-2 font-bold">Titre</label>
            <input type="text" name="titre" value="<?php echo htmlspecialchars($quiz['titre']); ?>" class="w-full border p-2 mb-4 rounded">
            
            <label class="block mb-2 font-bold">Catégorie</label>
            <select name="categorie_id" class="w-full border p-2 mb-4 rounded">
                <?php while($cat = $catStmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $quiz['id_categorie']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nom']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label class="block mb-2 font-bold">Description</label>
            <textarea name="description" class="w-full border p-2 mb-4 rounded"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
            
            <div class="flex justify-between">
                <a href="dashboard.php#quiz" class="text-gray-500 py-2">Annuler</a>
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded">Mettre à jour</button>
            </div>
        </form>
    </div>
</body>
</html>