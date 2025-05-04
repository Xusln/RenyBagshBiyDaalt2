<?php
session_start();
require_once 'db.php';

// Нэвтрээгүй бол login руу
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Сагс session-д хадгалах
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 'id' параметрийг авах
$id = $_GET['id'] ?? 0; // id параметр байхгүй бол 0 гэж тохирно

$stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
$stmt->execute([$id]);
$book = $stmt->fetch();
if (!$book) exit('Ном олдсонгүй!');

// Сагсанд нэмэх үйлдэл
if (!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = [
        'title' => $book['title'],
        'quantity' => 1,
        'price' => $book['price']
    ];
} else {
    $_SESSION['cart'][$id]['quantity'] += 1;
}



$order_msg = '';
if (isset($_POST['order_submit'])) {
    $uid = $_SESSION['user_id'];

    // Захиалга өгөх үед номын үлдэгдлийг DB-с дахин шалгах
    $stmt = $pdo->prepare("SELECT available_copies FROM books WHERE id=?");
    $stmt->execute([$id]);
    $bookData = $stmt->fetch();

    if ($bookData && $bookData['available_copies'] > 0) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? AND book_id=? AND status='pending'");
        $stmt->execute([$uid, $id]);
        if ($stmt->fetch()) {
            $order_msg = 'Та энэ номыг захиалчихсан байна!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO orders (book_id, user_id) VALUES (?, ?)");
            $stmt->execute([$id, $uid]);
            $stmt = $pdo->prepare("UPDATE books SET available_copies=available_copies-1 WHERE id=?");
            $stmt->execute([$id]);
            $order_msg = 'Захиалга амжилттай!';
            header("Refresh:0");
        }
    } else {
        $order_msg = 'Энэ ном дууссан байна!';
    }
}

// --- Зөвхөн энгийн хэрэглэгчид сэтгэгдэл, үнэлгээ оруулах санал зөвшөөрнө
if (empty($_SESSION['is_admin'])) {
    // Сэтгэгдэл нэмэх
    if (isset($_POST['comment_submit']) && !empty($_POST['comment'])) {
        $comment = $_POST['comment'];
        $uid = $_SESSION['user_id'];
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, book_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$uid, $id, $comment]);
    }

    // Үнэлгээ өгөх
    if (isset($_POST['rating_submit']) && isset($_POST['rating'])) {
        $rating = (int)$_POST['rating'];
        $uid = $_SESSION['user_id'];
        // Өмнө үнэлсэн эсэхийг шалгана
        $stmt = $pdo->prepare("SELECT * FROM ratings WHERE user_id=? AND book_id=?");
        $stmt->execute([$uid, $id]);
        $r = $stmt->fetch();
        if (!$r)
            $stmt = $pdo->prepare("INSERT INTO ratings (user_id, book_id, rating) VALUES (?, ?, ?)");
        else
            $stmt = $pdo->prepare("UPDATE ratings SET rating=? WHERE user_id=? AND book_id=?");
        if (!$r) $stmt->execute([$uid, $id, $rating]);
        else $stmt->execute([$rating, $uid, $id]);
    }
}

// Дундаж үнэлгээ
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as cnt FROM ratings WHERE book_id=?");
$stmt->execute([$id]);
$rateinfo = $stmt->fetch();
    
$stmt = $pdo->prepare("UPDATE books SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$id]);
// Сэтгэгдлүүд
$comments = $pdo->prepare("SELECT c.*, u.name FROM comments c JOIN users u ON c.user_id=u.id WHERE c.book_id=? ORDER BY c.created_at DESC");
$comments->execute([$id]);
?>
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($book['title']) ?></title>
    <style>
        /* Ерөнхий стилинг */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: rgb(249, 249, 249);
            color: #333;
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
            padding: 5px 15px;
            font-weight: bold;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        header .logout:hover,
        header .admin-link:hover {
            background-color: #34495e;
        }

        /* Контейнер */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Номын дэлгэрэнгүй мэдээлэл */
        .book-details {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px;
        max-width: 500px; /* book-details хэсгийн өргөнийг хязгаарлах */
        margin: 0 auto; /* Дундаж байрлалд байршуулах */
    }

    .book-info {
        max-width: 55%; /* Мэдээллийн хэсгийг 55%-д хязгаарлах */
        margin-right: 20px;
    }

    .book-image img {
        max-width: 200px; /* Зургийн өргөн */
        height: auto;
        border-radius: 8px;
    }

    .book-image {
        max-width: 40%; /* Зургийн хэсгийн өргөнийг хязгаарлах */
    }

        .book-details h2 {
            margin-top: 0;
        }

        .book-details p {
            font-size: 16px;
            line-height: 1.6;
        }

        .book-details img {
            max-width: 200px;
            margin-top: 15px;
        }

        /* Захиалга, сэтгэгдэл, үнэлгээний хэсэг */
        .order-section,
        .comment-section,
        .rating-section {
            margin-top: 30px;
        }

        .order-section button,
        .rating-section button {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .order-section button:hover,
        .rating-section button:hover {
            background-color: #2980b9;
        }

        .rating select,
        .comment-section textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .rating-section label {
            font-weight: bold;
        }

        /* Сэтгэгдэл, үнэлгээний хэсэг */
        .comment {
    border-bottom: 1px solid #ddd;
    margin-bottom: 10px;
    padding-bottom: 5px;
    background-color: #f9f9f9;
    padding: 10px;
    border-radius: 5px;
}

.comment p {
    margin: 0;
}

.comment small {
    color: #888;
    font-size: 0.9em;
}
        .message {
            color: blue;
            font-weight: bold;
        }
        .star-rating {
    direction: rtl;
    display: inline-flex;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 30px;
    color: #ccc;
    cursor: pointer;
    transition: color 0.2s;
}

.star-rating input:checked ~ label {
    color: gold;
}

.star-rating label:hover,
.star-rating label:hover ~ label {
    color: gold;
}
/* Буцах товч */
/* Буцах товчийг баруун дээд буланд байрлуулах */
.back-button {
    position: absolute; /* Товчийг absolute байрлалд байрлуулж */
    top: 20px; /* Дээд талд 20px зайтай */
    right: 20px; /* Баруун талд 20px зайтай */
}

.back-button button {
    background-color: #2ecc71;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.back-button button:hover {
    background-color: #27ae60;
}
    </style>
</head>
<body>
<header>
    <h1>Номын дэлгэрэнгүй</h1>
</header>

<div class="container">
<div class="book-details">
        <div class="book-info">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <p><b>Зохиогч:</b> <?= htmlspecialchars($book['author']) ?></p>
        <p><b>Огноо:</b> <?= htmlspecialchars($book['publication_year']) ?></p>
        <p><b>ISBN:</b> <?= htmlspecialchars($book['isbn']) ?></p>
        <p><b>Үнэ:</b> <?= htmlspecialchars($book['price']) ?>₮</p>
        <p><b>Ангилал:</b> <?= htmlspecialchars($book['category']) ?></p> <!-- Ангилал -->
        <p><b>Хэл:</b> <?= htmlspecialchars($book['language']) ?></p> <!-- Хэл -->
        <p><b>Тайлбар:</b> <?= nl2br(htmlspecialchars($book['description'])) ?></p>
        </div>
        <div class="book-image">
            <?php if($book['image']): ?>
                <img src="assets/<?= htmlspecialchars($book['image']) ?>" alt="Номын зураг">
            <?php endif; ?>
        </div>
    </div>
    <p><b>Захиалагдсан хувь:</b> <?= ($book['available_copies'] > 0) ? round((1 - $book['available_copies'] / $book['total_copies']) * 100, 2) . "%" : "Нөөц дууссан" ?></p>
    <div class="order-section">
        <form method="post">
            <button type="submit" name="order_submit">Энэ номыг захиалах</button>
        </form>
        <?php if($order_msg): ?>
            <p class="message"><?= $order_msg ?></p>
        <?php endif; ?>
    </div>
    <form id="add-to-cart-form" method="post" style="display: inline;">
    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">

    <a href="cart.php" onclick="this.closest('form').submit();" 
       class="add-to-cart-link" 
       style="
            display: inline-block;
            padding: 10px 20px;
            font-family: 'Arial', sans-serif;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top:20px;
        ">Сагсанд нэмэх</a>
</form>
    <hr>

    <div class="rating-section">
    <h3>Дундаж үнэлгээ: <?= $rateinfo['avg_rating'] ? round($rateinfo['avg_rating'], 1) . ' / 5' : 'Үнэлгээ алга' ?> (<?= $rateinfo['cnt'] ?> үнэлгээ)</h3>
    <?php if (empty($_SESSION['is_admin'])): ?>
        <form name="rating_form" method="post">
            <div class="star-rating">
                <input type="radio" name="rating" value="5" id="5"><label for="5">★</label>
                <input type="radio" name="rating" value="4" id="4"><label for="4">★</label>
                <input type="radio" name="rating" value="3" id="3"><label for="3">★</label>
                <input type="radio" name="rating" value="2" id="2"><label for="2">★</label>
                <input type="radio" name="rating" value="1" id="1"><label for="1">★</label>
            </div>
            <button type="submit" name="rating_submit">Үнэлэх</button>
        </form>
    <?php endif; ?>
</div>

<hr>

<div class="comment-section">
    <h3>Сэтгэгдэл</h3>
    <form name="comment_form" method="post">
        <textarea name="comment" required cols="40" rows="3"></textarea><br>
        <button type="submit" name="comment_submit">Сэтгэгдэл нэмэх</button>
    </form>

    <?php foreach ($comments as $comment): ?>
        <div class="comment">
            <p><strong><?= htmlspecialchars($comment['name']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></p>
            <small><?= htmlspecialchars($comment['created_at']) ?></small>
        </div>
    <?php endforeach; ?>
</div>
</div>
<div class="back-button">
    <a href="index.php"><button>Буцах</button></a> <!-- 'books.php' бол таны номын жагсаалтыг агуулсан хуудсанд шилжих линк юм -->
</div>
<script>
    document.querySelector("form[name='rating_form']").addEventListener("submit", function(event) {
        let selected = document.querySelector("input[name='rating']:checked");
        if (!selected) {
            event.preventDefault();
            alert("Та үнэлгээ сонгоно уу!");
        } else {
            alert("Таны үнэлгээ амжилттай хадгалагдлаа!");
        }
    });
</script>

<script>
    // Сэтгэгдэл оруулах үйлдлийг дэмжих
    document.querySelector("form[name='comment_form']").addEventListener("submit", function(event) {
        event.preventDefault();
        let commentText = document.querySelector("textarea[name='comment']").value;

        if (commentText) {
            let newComment = document.createElement("div");
            newComment.classList.add("comment");
            newComment.innerHTML = `<p><strong>Таны сэтгэгдэл:</strong> ${commentText}</p><small>Оруулсан цаг: ${new Date().toLocaleString()}</small>`;

            document.querySelector(".comment-section").appendChild(newComment);
            document.querySelector("textarea[name='comment']").value = ""; // Сэтгэгдлийг хоослох
        } else {
            alert("Сэтгэгдэл бичнэ үү!");
        }
    });
</script>
<script>
    // Захиалга амжилттай эсвэл алдаа гарсан тохиолдолд мэдэгдэл гаргах
    <?php if ($order_msg): ?>
        window.onload = function() {
            let orderMessage = "<?php echo $order_msg; ?>";
            alert(orderMessage); // Мэдэгдэл харуулах
        }
    <?php endif; ?>
</script>

</body>
</html>
