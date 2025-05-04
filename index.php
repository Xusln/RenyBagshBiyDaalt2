<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE :search_title OR author LIKE :search_author");
    $stmt->execute([
        'search_title' => "%$search%",
        'search_author' => "%$search%"
    ]);
    $books = $stmt->fetchAll();
} else {
    $books = $pdo->query("SELECT * FROM books")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>Номын сан</title>
    <style>
        /* Ерөнхий стилинг */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

/* Головны хэсэг */
header {
    background-color: #2c3e50;
    color: white;
    padding: 20px;
    text-align: center;
}

header .user-actions {
    margin-top: 10px;
}

header .logout,
header .admin-link {
    color: #ecf0f1;
    text-decoration: none;
    padding: 5px 10px;
    font-weight: bold;
}

header .logout:hover,
header .admin-link:hover {
    background-color: #34495e;
    border-radius: 5px;
}

/* Хайлтын хэсэг */
.search-section {
    text-align: center;
    margin: 20px 0;
}

.search-form {
    display: inline-block;
    margin-bottom: 20px;
}

.search-input {
    padding: 10px;
    font-size: 16px;
    width: 300px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.search-button {
    padding: 10px 15px;
    font-size: 16px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.search-button:hover {
    background-color: #2980b9;
}

/* Буцах линк */
.reset-search {
    display: inline-block;
    margin-top: 10px;
    color: #3498db;
    font-weight: bold;
    text-decoration: none;
}

.reset-search:hover {
    text-decoration: underline;
}

/* Номын жагсаалт */
.books-section {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.books-table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.books-table th, .books-table td {
    padding: 12px;
    text-align: left;
}

.books-table th {
    background-color: #2c3e50;
    color: white;
}

.books-table td {
    background-color: #ecf0f1;
}

.details-link {
    color: #3498db;
    text-decoration: none;
    font-weight: bold;
}

.details-link:hover {
    text-decoration: underline;
}
/* Миний сагс товч */
.cart-button {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
}

.cart-button button {
    padding: 12px 20px;
    background-color: #27ae60;
    color: white;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.cart-button button:hover {
    background-color: #2ecc71;
    transform: translateY(-3px); /* Товчны даралтын эффектийг нэмэх */
}

.cart-button button:active {
    background-color: #1f8e47;
    transform: translateY(0); /* Бүтэн товч дарагдсан үед */
}
    </style>
</head>
<body>
    <header>
        <h1>Номын сан</h1>
        <div class="user-actions">
            <a href="logout.php" class="logout">Гарах</a>
            <?php if($_SESSION['is_admin']) echo ' | <a href="admin.php" class="admin-link">Админ хэсэг</a>'; ?>
        </div>
    </header>

    <!-- Хайлтын хэсэг -->
    <section class="search-section">
        <form method="get" class="search-form">
            <input type="text" name="search" class="search-input" placeholder="Ном эсвэл зохиогчоор хайх" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-button">Хайх</button>
        </form>
        <a href="cart.php" class="cart-button">
    <button>Миний сагс</button>
</a>    
        <?php if ($search): ?>
            <a href="index.php" class="reset-search">Бүх номыг харах</a>
        <?php endif; ?>
    </section>

    <!-- Номын жагсаалт -->
    <section class="books-section">
        <table class="books-table">
            <thead>
                <tr>
                    <th>Нэр</th>
                    <th>Зохиогч</th>
                    <th>Он</th>
                    <th>Дэлгэрэнгүй</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($books as $b): ?>
                <tr>
                    <td><?= htmlspecialchars($b['title']) ?></td>
                    <td><?= htmlspecialchars($b['author']) ?></td>
                    <td><?= htmlspecialchars($b['publication_year']) ?></td>
                    <td><a href="book.php?id=<?= $b['id'] ?>" class="details-link">Дэлгэрэнгүй</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <script document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    // Хайлт хийх хэсэгт focus хийх үед өнгийг өөрчлөх
    searchInput.addEventListener('focus', function() {
        searchInput.style.borderColor = '#3498db';
    });

    // Хайлтын талбараас гарсан үед өнгийг буцаах
    searchInput.addEventListener('blur', function() {
        searchInput.style.borderColor = '#ccc';
    });
});
></script>
</body>
</html>
