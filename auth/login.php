<?php
session_start();
require_once '../config/Database.php';
require_once '../classes/User.php';

$message = "";

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Compte créé avec succès! Veuillez vous connecter.";
    $messageType = "green";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);

    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($user->login($email, $password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_nom'] = $user->nom;
        $_SESSION['user_role'] = $user->role;

        if ($user->role == 'enseignant') {
            header("Location: ../enseignant/dashboard.php");
        } else {
            header("Location: ../etudiant/dashboard.php");
        }
        exit;
    } else {
        $message = "Email ou mot de passe incorrect.";
        $messageType = "red";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-96">
        <h2 class="text-3xl font-bold mb-6 text-center text-indigo-600">Connexion</h2>
        
        <?php if($message): ?>
            <div class="bg-<?php echo $messageType; ?>-100 text-<?php echo $messageType; ?>-700 p-3 mb-4 rounded text-sm text-center font-medium border border-<?php echo $messageType; ?>-200">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Mot de passe</label>
                <input type="password" name="password" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full bg-indigo-600 text-white p-3 rounded-lg hover:bg-indigo-700 transition font-bold text-lg shadow-md">
                Se connecter
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
            Pas encore de compte ? <a href="register.php" class="text-indigo-600 hover:underline font-semibold">S'inscrire</a>
        </p>
    </div>
</body>
</html>