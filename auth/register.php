<?php
// auth/register.php
require_once '../config/Database.php';
require_once '../classes/User.php';

$message = "";
$messageType = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($user->register($nom, $email, $password, $role)) {
        header("Location: login.php?success=1");
        exit; 
    } else {
        $message = "Erreur! Cet email existe déjà.";
        $messageType = "red";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen pb-20">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Créer un compte</h2>
        
        <?php if($message): ?>
            <div class="bg-<?php echo $messageType; ?>-100 text-<?php echo $messageType; ?>-700 p-3 mb-4 rounded text-sm text-center font-medium border border-<?php echo $messageType; ?>-200">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Nom complet</label>
                <input type="text" name="nom" placeholder="Ex: Ahmed Alami" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" placeholder="Ex: ahmed@gmail.com" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Vous êtes ?</label>
                <select name="role" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="etudiant">Étudiant </option>
                    <option value="enseignant">Enseignant </option>
                </select>
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 transition font-bold text-lg shadow-md">
                S'inscrire
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
            Vous avez déjà un compte ? <a href="login.php" class="text-indigo-600 hover:underline font-semibold">Se connecter</a>
        </p>
    </div>
</body>
</html>