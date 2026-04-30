<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur - MediFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h1 class="text-2xl font-bold text-red-600 mb-4">Erreur</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
                    <?php foreach ($errors as $error): ?>
                        <p class="mb-2"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <a href="?action=products&method=list" class="block text-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Retour à la liste
            </a>
        </div>
    </div>
</body>
</html>
