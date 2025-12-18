<?php
session_start();
// الحماية: إيلا ماكانش مكونيكطي ولا ماشي طالب، صيفطو لـ login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'etudiant') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Étudiant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded shadow">
        <h1 class="text-3xl font-bold text-green-600 mb-4">Espace Étudiant 🎓</h1>
        <p class="text-xl">Bonjour, <span class="font-bold"><?php echo htmlspecialchars($_SESSION['nom']); ?></span> !</p>
        <p class="mt-4 text-gray-600">مبروك! راك دخلتي بنجاح كطالب.</p>
        
        <a href="../logout.php" class="inline-block mt-6 bg-red-500 text-white px-4 py-2 rounded">Déconnexion</a>
    </div>
</body>
</html>