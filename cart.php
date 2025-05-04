<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// cart-ыг Session-оос авах
$cart = $_SESSION['cart'] ?? [];

// Сагсыг цэвэрлэх
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    $cart = [];
}

// Захиалах үйлдэл
if (isset($_POST['order_cart']) && $cart) {
    $user_id = $_SESSION['user_id'];
    foreach(array_keys($cart) as $bookid) {
        // Ном байна уу, хувь байгаа эсэх шалгах
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id=? AND available_copies > 0");
        $stmt->execute([$bookid]);
        $book = $stmt->fetch();

        if ($book) {
            // Давхар захиалаагүй эсэх шалгах
            $stmt2 = $pdo->prepare("SELECT * FROM orders WHERE user_id=? AND book_id=? AND status='pending'");
            $stmt2->execute([$user_id, $bookid]);

            if (!$stmt2->fetch()) {
                $pdo->prepare("INSERT INTO orders (book_id, user_id) VALUES (?, ?)")->execute([$bookid, $user_id]);
                $pdo->prepare("UPDATE books SET available_copies=available_copies-1 WHERE id=?")->execute([$bookid]);
            }
        }
    }
    unset($_SESSION['cart']);
    $cart = [];
    $msg = "Сагсанд байсан бүх ном амжилттай захиалагдлаа!";
}

// Ном устгах
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>Миний сагс</title>
    <style>/* Загварын үндсэн хэсэг */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

/* Гарчиг */
h2 {
    text-align: center;
    color: #333;
    padding: 20px 0;
}

/* Сагсны жагсаалт */
ul {
    list-style-type: none;
    padding: 0;
    margin: 20px;
}

/* Номын элементүүд */
li {
    background-color: #fff;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: nowrap; /* Хөндлөн шахахгүй */
    width: 150px; /* Өргөнийг нь 100%-д тохируулна */
}

/* Устгах товч */
a {
    text-decoration: none;
    color: #e74c3c;
    font-weight: bold;
}

/* Устгах товч дээр hover хийх */
a:hover {
    color: #c0392b;
}

/* Захиалах болон сагсыг цэвэрлэх товч */
form button {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #2980b9;
}

/* Буцах холбоос */
a[href="index.php"] {
    display: block;
    text-align: center;
    color: #3498db;
    margin-top: 30px;
    font-size: 18px;
    text-decoration: none;
}

a[href="index.php"]:hover {
    color: #2980b9;
}
</style>
</head>
<body>
    <h2>🛒 Миний сагс</h2>

    <?php if (!empty($msg)): ?>
        <p style="color: blue;"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <?php if ($cart): ?>
        <ul>
            <?php foreach ($cart as $bid => $btitle): ?>
                <li>
                    <?php
                    // title шалгах
                    if (is_array($btitle)) {
                        $title = $btitle['title'] ?? 'Нэр тодорхойгүй';
                    } else {
                        $title = $btitle ?: 'Нэр тодорхойгүй';
                    }
                    echo htmlspecialchars($title); // Зөвхөн номын гарчиг харуулах
                    ?>
                    <a href="?remove=<?= urlencode($bid) ?>">[Устгах]</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <form method="post">
            <button type="submit" name="order_cart">Бүгдийг захиалах</button>
            <button type="submit" name="clear_cart">Сагсыг цэвэрлэх</button>
        </form>
    <?php else: ?>
        <p>Таны сагс хоосон байна.</p>
    <?php endif; ?>

    <a href="index.php">📚 Номын жагсаалт руу буцах</a>

    <script >document.addEventListener('DOMContentLoaded', function() {
    // Устгах товчлуурын үйлдэл
    const removeLinks = document.querySelectorAll('a[href^="?remove="]');
    removeLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const listItem = e.target.closest('li');
            // Анимаци хийх
            listItem.style.opacity = '0';
            setTimeout(() => {
                listItem.style.display = 'none';
            }, 300); // 300ms дараа хасах
        });
    });
});
</script> <!-- JS файл -->
</body>
</html>