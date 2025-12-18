<?php
session_start();

// التحقق من أن المستخدم أستاذ
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header("Location: ../auth/login.php");
    exit;
}

require_once '../config/Database.php';
require_once '../classes/Category.php';
require_once '../classes/Quiz.php';

$database = new Database();
$db = $database->getConnection();
$user_id = $_SESSION['user_id'];

$categoryObj = new Category($db);
$stmtCategories = $categoryObj->readAllByTeacher($user_id);
$allCategories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
$categoriesCount = count($allCategories);

$quizObj = new Quiz($db);
$stmtQuizzes = $quizObj->readAllByTeacher($user_id);
$quizzesCount = $stmtQuizzes->rowCount();

$queryQuestions = "SELECT COUNT(*) as total_questions FROM questions q 
                   JOIN quiz z ON q.id_quiz = z.id 
                   WHERE z.id_enseignant = :uid";
$stmtQ = $db->prepare($queryQuestions);
$stmtQ->execute([':uid' => $user_id]);
$totalQuestions = $stmtQ->fetch(PDO::FETCH_ASSOC)['total_questions'];

$queryStudents = "SELECT COUNT(DISTINCT id_etudiant) as total_students FROM resultats r 
                  JOIN quiz z ON r.id_quiz = z.id 
                  WHERE z.id_enseignant = :uid";
$stmtS = $db->prepare($queryStudents);
$stmtS->execute([':uid' => $user_id]);
$totalStudents = $stmtS->fetch(PDO::FETCH_ASSOC)['total_students'];

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Enseignant - Qodex</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .section-content { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Qodex</span>
                </div>
                <div class="hidden md:ml-10 md:flex md:space-x-8">
                    <a href="#dashboard" onclick="showSection('dashboard')" class="text-gray-900 px-3 py-2 rounded-md text-sm font-medium hover:text-indigo-600 transition">Vue d'ensemble</a>
                    <a href="#categories" onclick="showSection('categories')" class="text-gray-900 px-3 py-2 rounded-md text-sm font-medium hover:text-indigo-600 transition">Catégories</a>
                    <a href="#quiz" onclick="showSection('quiz')" class="text-gray-900 px-3 py-2 rounded-md text-sm font-medium hover:text-indigo-600 transition">Mes Quiz</a>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700 font-medium hidden sm:block">Prof. <?php echo htmlspecialchars($_SESSION['user_nom']); ?></span>
                    <a href="../logout.php" class="text-red-500 hover:text-red-700 font-bold text-sm bg-red-50 px-3 py-2 rounded-full transition" title="Déconnexion">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        
        <div id="dashboard" class="section-content">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 border-l-4 border-blue-600 pl-4">Tableau de Bord</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-indigo-500 hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
                            <i class="fas fa-layer-group text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-800"><?php echo $categoriesCount; ?></span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Catégories Actives</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-purple-500 hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-full text-purple-600">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-800"><?php echo $quizzesCount; ?></span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Quiz Créés</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-blue-500 hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                            <i class="fas fa-question text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-800"><?php echo $totalQuestions; ?></span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Questions au total</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md border-b-4 border-green-500 hover:shadow-lg transition transform hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-full text-green-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <span class="text-3xl font-bold text-gray-800"><?php echo $totalStudents; ?></span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">Étudiants Participants</p>
                </div>
            </div>
        </div>

        <div id="categories" class="section-content">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 border-l-4 border-indigo-600 pl-4">Mes Catégories</h1>
                <button onclick="document.getElementById('catModal').classList.remove('hidden')" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700 shadow-lg transform hover:-translate-y-0.5 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Nouvelle Catégorie
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if($categoriesCount > 0): ?>
                    <?php foreach($allCategories as $cat): ?>
                        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-xl transition duration-300 relative group">
                            <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                <a href="edit_category.php?id=<?php echo $cat['id']; ?>" class="bg-blue-100 text-blue-600 p-2 rounded-full hover:bg-blue-600 hover:text-white transition" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="../actions/delete_category.php?id=<?php echo $cat['id']; ?>" 
                                   class="bg-red-100 text-red-600 p-2 rounded-full hover:bg-red-600 hover:text-white transition"
                                   onclick="return confirm('Attention: Supprimer cette catégorie supprimera aussi tous ses quiz. Continuer ?');" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                            <div class="mb-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded-full font-bold uppercase tracking-wide">
                                    ID: <?php echo $cat['id']; ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($cat['nom']); ?></h3>
                            <p class="text-gray-600 text-sm mb-4 h-12 overflow-hidden leading-relaxed"><?php echo htmlspecialchars($cat['description']); ?></p>
                            <div class="flex items-center text-indigo-600 text-sm font-semibold border-t pt-3">
                                <i class="fas fa-book-open mr-2"></i>
                                <?php echo $cat['quiz_count']; ?> Quiz associés
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Aucune catégorie trouvée.</p>
                        <button onclick="document.getElementById('catModal').classList.remove('hidden')" class="text-indigo-600 font-bold hover:underline mt-2">Créer votre première catégorie</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="quiz" class="section-content hidden">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 border-l-4 border-purple-600 pl-4">Mes Quiz</h1>
                <button onclick="document.getElementById('quizModal').classList.remove('hidden')" class="bg-purple-600 text-white px-5 py-2.5 rounded-lg hover:bg-purple-700 shadow-lg transform hover:-translate-y-0.5 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Créer un Quiz
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if($quizzesCount > 0): ?>
                    <?php while ($row = $stmtQuizzes->fetch(PDO::FETCH_ASSOC)): extract($row); ?>
                        <div class="bg-white p-6 rounded-xl shadow-md border-t-4 border-purple-500 hover:shadow-xl transition duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full font-bold border border-purple-100">
                                    <?php echo htmlspecialchars($categorie_nom); ?>
                                </span>
                                <div class="flex gap-2">
                                    <a href="edit_quiz.php?id=<?php echo $id; ?>" class="text-gray-400 hover:text-blue-500 transition" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../actions/delete_quiz.php?id=<?php echo $id; ?>" 
                                       class="text-gray-400 hover:text-red-500 transition"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?');" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <a href="add_question.php?id=<?php echo $id; ?>" class="group">
                                <h3 class="text-xl font-bold mb-2 text-gray-900 group-hover:text-purple-600 transition flex items-center gap-2">
                                    <?php echo htmlspecialchars($titre); ?>
                                    <i class="fas fa-external-link-alt text-xs opacity-0 group-hover:opacity-100 transition"></i>
                                </h3>
                            </a>
                            <p class="text-gray-600 text-sm mb-6 h-12 overflow-hidden leading-relaxed"><?php echo htmlspecialchars($description); ?></p>
                            <div class="border-t pt-4">
                                <a href="add_question.php?id=<?php echo $id; ?>" class="flex items-center justify-center w-full bg-gray-50 text-gray-700 px-4 py-2 rounded-lg hover:bg-purple-50 hover:text-purple-700 border border-gray-200 transition font-medium">
                                    <i class="fas fa-list-ul mr-2"></i> Voir les Questions
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-16 bg-white rounded-xl border-2 border-dashed border-gray-300">
                        <i class="fas fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Vous n'avez pas encore créé de quiz.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="catModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Ajouter une catégorie</h2>
            <form action="../actions/add_category.php" method="POST">
                <input type="text" name="nom" placeholder="Nom de la catégorie" required class="w-full border p-3 mb-4 rounded-lg">
                <textarea name="description" placeholder="Description..." rows="3" class="w-full border p-3 mb-4 rounded-lg"></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('catModal').classList.add('hidden')" class="px-5 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Annuler</button>
                    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <div id="quizModal" class="fixed inset-0 bg-black bg-opacity-60 hidden flex items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white p-8 rounded-2xl w-full max-w-md shadow-2xl">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Créer un Quiz</h2>
            <form action="../actions/add_quiz.php" method="POST">
                <input type="text" name="titre" placeholder="Titre du quiz" required class="w-full border p-3 mb-4 rounded-lg">
                <select name="categorie_id" required class="w-full border p-3 mb-4 rounded-lg bg-white">
                    <option value="">Sélectionner une catégorie</option>
                    <?php foreach($allCategories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="description" placeholder="Description..." rows="3" class="w-full border p-3 mb-4 rounded-lg"></textarea>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('quizModal').classList.add('hidden')" class="px-5 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Annuler</button>
                    <button type="submit" class="bg-purple-600 text-white px-5 py-2 rounded-lg hover:bg-purple-700">Créer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');
        }

        if(window.location.hash) {
            const section = window.location.hash.substring(1);
            if(document.getElementById(section)) showSection(section);
        } else {
            // Default show stats
            showSection('dashboard');
            // Show categories and quiz underneath as per original design? 
            // Or just tabs? Here I used tabs for cleaner look.
            // If you want everything on one page, remove "hidden" from other sections.
            // But tabs are better for stats.
            
            // Let's modify slightly to show stats AND categories AND quiz if user prefers scroll
            // But tabs are cleaner. Let's stick to tabs.
        }
    </script>
</body>
</html>