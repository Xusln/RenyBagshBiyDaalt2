<?php
require_once 'db.php';
$books = $pdo->query("SELECT * FROM books")->fetchAll();
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="utf-8">
    <title>Номуудын жагсаалт</title>
    <style>
        /* Ерөнхий хэв маяг */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fb;
    margin: 0;
    padding: 0;
}

/* Контейнер */
.container {
    width: 90%;
    max-width: 1200px;
    margin: 30px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Номуудын жагсаалт хуудас */
.page-title {
    text-align: center;
    font-size: 30px;
    color: #333;
    margin-bottom: 20px;
}

/* Номын мэдээллийг 2 колонктой үзүүлэх */
.book-details {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.book-item {
    width: 48%;
    background-color: #ffffff;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.book-info {
    margin-bottom: 20px;
}

.book-row {
    margin-bottom: 15px;
    font-size: 14px;
}

.book-row strong {
    color: #333;
    font-weight: bold;
}

.book-info img {
    margin-right: 10px;
    border-radius: 50%;
    border: 2px solid #ddd;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

/* Гарчиг, Зохиогч, Үнэ зэрэг мэдээллийг сайжруулах */
.book-info .book-row {
    font-size: 16px;
    color: #444;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.book-info .book-row strong {
    font-size: 16px;
    color: #555;
}

/* Товчлуурууд */
.btn {
    padding: 8px 18px;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin: 10px 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn-edit {
    background-color: #ff9800;
}

.btn-delete {
    background-color: #f44336;
}

.btn-edit:hover {
    background-color: #e68900;
}

.btn-delete:hover {
    background-color: #d32f2f;
}

.btn-back {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2196F3;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
    margin-top: 20px;
}

.btn-back:hover {
    background-color: #1976D2;
}

/* Багана засах */
@media (max-width: 768px) {
    .book-item {
        width: 100%;
    }
}

    </style>
</head>
<body>
<div class="container">
    <h2 class="page-title">Номуудын жагсаалт</h2>
    <div class="book-details">
        <?php foreach($books as $b): ?>
        <div class="book-item">
            <div class="book-info">
                <div class="book-row"><strong>#</strong> <?= $b['id'] ?></div>
                <div class="book-row"><strong>Гарчиг:</strong> <?= htmlspecialchars($b['title']) ?></div>
                <div class="book-row"><strong>Зохиогч:</strong> <?= htmlspecialchars($b['author']) ?></div>
                <div class="book-row"><strong>Он:</strong> <?= htmlspecialchars($b['publication_year']) ?></div>
                <div class="book-row"><strong>ISBN:</strong> <?= htmlspecialchars($b['isbn']) ?></div>
                <div class="book-row"><strong>Хэвлэлийн газар:</strong> <?= htmlspecialchars($b['publisher']) ?></div>
                <div class="book-row"><strong>Хийц:</strong> <?= htmlspecialchars($b['binding_type']) ?></div>
                <div class="book-row"><strong>Хуудас:</strong> <?= htmlspecialchars($b['pages']) ?></div>
                <div class="book-row"><strong>Хэл:</strong> <?= htmlspecialchars($b['language']) ?></div>
                <div class="book-row"><strong>Үнэ:</strong> <?= htmlspecialchars($b['price']) ?></div>
                <div class="book-row"><strong>Категори:</strong> <?= htmlspecialchars($b['category']) ?></div>
            </div>
            <div class="book-info">
                <div class="book-row"><strong>Сэдэв:</strong> <?= htmlspecialchars($b['subject']) ?></div>
                <div class="book-row">
                    <strong>Зураг:</strong> 
                    <?php if (!empty($b['image'])): ?>
                        <img src="assets/<?= htmlspecialchars($b['image']) ?>" alt="Image" width="70" height="70">
                    <?php else: ?>
                        Нэмэгдээгүй
                    <?php endif; ?>
                </div>
                <div class="book-row"><strong>Байршил:</strong> <?= htmlspecialchars($b['location']) ?></div>
                <div class="book-row"><strong>Нийт хувь:</strong> <?= htmlspecialchars($b['total_copies']) ?></div>
                <div class="book-row"><strong>Чөлөөт хувь:</strong> <?= htmlspecialchars($b['available_copies']) ?></div>
                <div class="book-row"><strong>Захиалсан хэрэглэгч ID:</strong> <?= htmlspecialchars($b['ordered_by']) ?></div>
                <div class="book-row"><strong>Тайлбар:</strong> <?= !empty($b['description']) ? '✔' : '❌' ?></div>
                <div class="book-row"><strong>Үзсэн тоо:</strong> <?= htmlspecialchars($b['view_count']) ?></div>
                <div class="book-row"><strong>Статус:</strong> <?= htmlspecialchars($b['status']) ?></div>
                <div class="book-row"><strong>Боть:</strong> <?= htmlspecialchars($b['edition']) ?></div>
                <div class="book-row">
                    <a href="update_book.php?id=<?= $b['id'] ?>" class="btn btn-edit">Засах</a>
                    <a href="delete_book.php?id=<?= $b['id'] ?>" class="btn btn-delete" onclick="return confirm('Устгах уу?')">Устгах</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <a href="admin.php" class="btn-back">← Админ хуудас руу буцах</a>
</div>

<script>// Жишээ нь, админ панелийн товчлуурын үйлдэл дээр хөдөлгөөн нэмэх
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function(event) {
        if (!confirm('Та энэ номыг устгахыг хүсч байна уу?')) {
            event.preventDefault();
        }
    });
});
</script>
</body>
</html>
